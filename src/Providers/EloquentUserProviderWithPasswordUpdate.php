<?php


namespace SamAsEnd\NeedsAutoRehash\Providers;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

class EloquentUserProviderWithPasswordUpdate extends EloquentUserProvider implements ProviderWithPasswordUpdate
{
    public function __construct(EloquentUserProvider $provider)
    {
        parent::__construct($provider->hasher, $provider->model);
    }

    public function updatePassword(Authenticatable $user, $plainPassword)
    {
        $user->password = $this->hasher->make($plainPassword);

        $timestamps = $user->timestamps;

        $user->timestamps = false;

        $user->save();

        $user->timestamps = $timestamps;
    }
}
