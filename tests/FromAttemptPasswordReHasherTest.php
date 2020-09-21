<?php

namespace SamAsEnd\NeedsAutoRehash\Tests;

use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use SamAsEnd\NeedsAutoRehash\Providers\ProviderWithPasswordUpdate;
use SamAsEnd\NeedsAutoRehash\UnexpectedProviderException;
use Throwable;

class FromAttemptPasswordReHasherTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadLaravelMigrations();
    }

    public function testThrowExceptionForUnknownProvider()
    {
        config(['auth.defaults.provider' => 'lorem ipsum']);

        try {
            Auth::guard('web')->attempt([
                'email' => '4sam21@gmail.com',
                'password' => 'password',
            ]);

            $this->fail('UnexpectedProviderException should have been thrown.');
        } catch (Throwable $exception) {
            $this->assertInstanceOf(UnexpectedProviderException::class, $exception);
        }
    }

    public function testEloquentPasswordUpdateRehash()
    {
        $this->testPasswordUpdateRehash();
    }

    public function testDatabasePasswordUpdateRehash()
    {
        config([
            'auth.providers.users' => [
                'driver' => 'database',
                'table' => 'users',
            ]
        ]);

        $this->testPasswordUpdateRehash();
    }

    public function testCustomUserProviderPasswordUpdateRehash()
    {
        Schema::dropIfExists('admins');

        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        $empty = new EmptyCustomerUserProvider(DB::connection(), app(Hasher::class), 'admins');

        $this->app->instance(ProviderWithPasswordUpdate::class, $empty);

        config(['auth.defaults.provider' => 'custom-provider']);
        config(['auth.providers.users.model' => Admin::class]);

        Auth::provider('custom-provider', function ($app, array $config) use ($empty) {
            return $empty;
        });

        // FIXME: Load the User provider properly

        $this->testPasswordUpdateRehash('admins', 'web');
    }

    public function testPasswordUpdateRehash($table = 'users', $guard = 'web')
    {
        DB::table($table)->insert([
            'name' => 'Samson Endale',
            'email' => '4sam21@gmail.com',
            'password' => $hash = password_hash('password', PASSWORD_BCRYPT, ['cost' => 4]),
        ]);

        config(['bcrypt.rounds' => 5]);

        $this->assertDatabaseHas($table, [
            'name' => 'Samson Endale',
            'email' => '4sam21@gmail.com',
            'password' => $hash
        ]);

        Auth::guard($guard)->attempt([
            'email' => '4sam21@gmail.com',
            'password' => 'password',
        ]);

        $this->assertDatabaseCount($table, 1);

        $this->assertDatabaseMissing($table, [
            'name' => 'Samson Endale',
            'email' => '4sam21@gmail.com',
            'password' => $hash
        ]);
    }
}
