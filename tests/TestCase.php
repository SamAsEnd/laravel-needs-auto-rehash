<?php

namespace SamAsEnd\NeedsAutoRehash\Tests;

use SamAsEnd\NeedsAutoRehash\RehashServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [RehashServiceProvider::class];
    }
}
