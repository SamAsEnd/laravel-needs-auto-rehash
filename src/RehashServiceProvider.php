<?php

namespace SamAsEnd\NeedsAutoRehash;

use Illuminate\Auth\Events\Attempting;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class RehashServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        Event::listen(Attempting::class, FromAttemptPasswordReHasher::class);
    }
}
