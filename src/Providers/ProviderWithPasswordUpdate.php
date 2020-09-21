<?php

namespace SamAsEnd\NeedsAutoRehash\Providers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;

interface ProviderWithPasswordUpdate extends UserProvider
{
    public function updatePassword(Authenticatable $user, $plainPassword);
}
