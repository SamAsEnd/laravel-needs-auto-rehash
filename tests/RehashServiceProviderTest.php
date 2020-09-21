<?php

namespace SamAsEnd\NeedsAutoRehash\Tests;

use Illuminate\Auth\Events\Attempting;
use SamAsEnd\NeedsAutoRehash\FromAttemptPasswordReHasher;
use SamAsEnd\NeedsAutoRehash\RehashServiceProvider;

class RehashServiceProviderTest extends TestCase
{
    public function testServiceIsLoaded()
    {
        $this->assertTrue($this->app->providerIsLoaded(RehashServiceProvider::class),
            'RehashServiceProvider is not loaded');
    }

    public function testFromAttemptPasswordReHasherIsListening()
    {
        $guard = 'some-guard';
        $credentials = ['email' => 'some', 'password' => 'test'];
        $remember = false;

        $this->mock(FromAttemptPasswordReHasher::class, function ($mock) use ($guard, $credentials, $remember) {
            $mock->shouldReceive('handle')
                ->withArgs(function (Attempting $attempting) use ($guard, $credentials, $remember) {
                    return
                        $attempting->guard === $guard &&
                        $attempting->credentials == $credentials &&
                        $attempting->remember === $remember;
                })
                ->once();
        });

        event(new Attempting($guard, $credentials, $remember));
    }
}
