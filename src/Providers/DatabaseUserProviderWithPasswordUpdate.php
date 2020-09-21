<?php

namespace SamAsEnd\NeedsAutoRehash\Providers;

use Illuminate\Auth\DatabaseUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

class DatabaseUserProviderWithPasswordUpdate extends DatabaseUserProvider implements ProviderWithPasswordUpdate
{
    public function __construct(DatabaseUserProvider $provider)
    {
        parent::__construct($provider->conn, $provider->hasher, $provider->table);
    }

    public function updatePassword(Authenticatable $user, $plainPassword)
    {
        $this->conn->table($this->table)
            ->where($user->getAuthIdentifierName(), $user->getAuthIdentifier())
            ->update(['password' => $this->hasher->make($plainPassword)]);
    }
}
