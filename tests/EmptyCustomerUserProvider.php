<?php

namespace SamAsEnd\NeedsAutoRehash\Tests;

use Illuminate\Auth\DatabaseUserProvider;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Database\ConnectionInterface;
use SamAsEnd\NeedsAutoRehash\Providers\DatabaseUserProviderWithPasswordUpdate;

class EmptyCustomerUserProvider extends DatabaseUserProviderWithPasswordUpdate
{
    public function __construct(ConnectionInterface $conn, HasherContract $hasher, $table)
    {
        parent::__construct(new DatabaseUserProvider($conn, $hasher, $table));
    }
}
