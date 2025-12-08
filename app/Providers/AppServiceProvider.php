<?php

namespace App\Providers;

use App\Contracts\Lib\Encryptor;
use App\Lib\LaravelHashEncryptor;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        app()->bind(Encryptor::class, LaravelHashEncryptor::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
