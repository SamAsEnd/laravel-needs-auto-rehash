<?php

namespace SamAsEnd\NeedsAutoRehash\Tests;

use SamAsEnd\NeedsAutoRehash\RehashServiceProvider;

class RehashServiceProviderTest extends TestCase
{
    public function testServiceIsLoaded()
    {
        $this->assertTrue($this->app->providerIsLoaded(RehashServiceProvider::class), 'RehashServiceProvider is not loaded');
    }
}
