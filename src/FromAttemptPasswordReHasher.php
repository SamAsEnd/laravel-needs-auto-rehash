<?php


namespace SamAsEnd\NeedsAutoRehash;

use Illuminate\Auth\DatabaseUserProvider;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Auth\Events\Attempting;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Factory;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Hashing\Hasher;
use SamAsEnd\NeedsAutoRehash\Providers\DatabaseUserProviderWithPasswordUpdate;
use SamAsEnd\NeedsAutoRehash\Providers\EloquentUserProviderWithPasswordUpdate;
use SamAsEnd\NeedsAutoRehash\Providers\ProviderWithPasswordUpdate;

class FromAttemptPasswordReHasher
{
    /** @var ProviderWithPasswordUpdate */
    protected $provider;

    /** @var Factory */
    protected $auth;

    /** @var Container */
    private $container;

    /** @var Hasher */
    private $hasher;

    public function __construct(Container $container, UserProvider $provider, Factory $auth, Hasher $hasher)
    {
        $this->container = $container;
        $this->provider = $this->extendsWithPasswordUpdate($provider);
        $this->auth = $auth;
        $this->hasher = $hasher;
    }

    protected function extendsWithPasswordUpdate(UserProvider $provider): ProviderWithPasswordUpdate
    {
        if ($provider instanceof DatabaseUserProvider) {
            return new DatabaseUserProviderWithPasswordUpdate($provider);
        }

        if ($provider instanceof EloquentUserProvider) {
            return new EloquentUserProviderWithPasswordUpdate($provider);
        }

        try {
            return $this->container->make(ProviderWithPasswordUpdate::class);
        } catch (BindingResolutionException $e) {
            throw new UnexpectedProviderException(get_class($this->provider).' is not expected.', $e);
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
        return $this->hasher->needsRehash($user->getAuthPassword());
    }

    protected function passwordUpdateRehash(Authenticatable $user, $password)
    {
        $this->provider->updatePassword($user, $password);
    }
}
