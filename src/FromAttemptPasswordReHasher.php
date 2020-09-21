<?php


namespace SamAsEnd\NeedsAutoRehash;

use Illuminate\Auth\CreatesUserProviders;
use Illuminate\Auth\DatabaseUserProvider;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Auth\Events\Attempting;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Factory as AuthContract;
use Illuminate\Contracts\Config\Repository as ConfigContract;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container as ContainerContract;
use Illuminate\Contracts\Hashing\Hasher as HashContract;
use SamAsEnd\NeedsAutoRehash\Providers\DatabaseUserProviderWithPasswordUpdate;
use SamAsEnd\NeedsAutoRehash\Providers\EloquentUserProviderWithPasswordUpdate;
use SamAsEnd\NeedsAutoRehash\Providers\ProviderWithPasswordUpdate;

class FromAttemptPasswordReHasher
{
    use CreatesUserProviders;

    /** @var  ContainerContract */
    private $app;

    /** @var AuthContract */
    protected $auth;

    /** @var HashContract */
    private $hash;

    /** @var ConfigContract */
    private $config;

    /** @var ProviderWithPasswordUpdate */
    protected $provider;

    public function __construct(
        ContainerContract $container,
        AuthContract $auth,
        HashContract $hash,
        ConfigContract $config
    ) {
        $this->app = $container;
        $this->auth = $auth;
        $this->hash = $hash;
        $this->config = $config;

        $this->provider = $this->getUserProviderWithPasswordUpdate();
    }

    protected function getUserProviderWithPasswordUpdate(): ProviderWithPasswordUpdate
    {
        $provider = $this->createUserProvider($this->getUserProviderName());

        if ($provider instanceof DatabaseUserProvider) {
            return new DatabaseUserProviderWithPasswordUpdate($provider);
        }

        if ($provider instanceof EloquentUserProvider) {
            return new EloquentUserProviderWithPasswordUpdate($provider);
        }

        try {
            return $this->app->make(ProviderWithPasswordUpdate::class);
        } catch (BindingResolutionException $e) {
            throw new UnexpectedProviderException('ProviderWithPasswordUpdate could not be found.', $e);
        }
    }

    public function handle(Attempting $event)
    {
        $user = $this->provider->retrieveByCredentials($event->credentials);

        if (! is_null($user) && $this->validCredentials($event) && $this->passwordNeedsRehash($user)) {
            $this->passwordUpdateRehash($user, $event->credentials['password']);
        }
    }

    protected function validCredentials(Attempting $attempting): bool
    {
        return $this->auth->guard($attempting->guard)->validate($attempting->credentials);
    }

    protected function passwordNeedsRehash(Authenticatable $user)
    {
        return $this->hash->needsRehash($user->getAuthPassword());
    }

    protected function passwordUpdateRehash(Authenticatable $user, $password)
    {
        $this->provider->updatePassword($user, $password);
    }

    protected function getUserProviderName()
    {
        $guard = $this->config->get('auth.defaults.guard');

        return
            $this->getDefaultUserProvider() ??
            $this->config->get('auth.guards.'.$guard.'.provider');
    }
}
