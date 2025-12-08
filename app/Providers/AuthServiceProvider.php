<?php

namespace App\Providers;

use App\Contracts\Services\AuthInterface;
use App\Services\JwtAuthService;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $concreteImplementation = match (config('auth.driver')) {
            'jwt' => JwtAuthService::class,
            // 'sanctum' => '',
            default => JwtAuthService::class,
        };

        app()->bind(AuthInterface::class, $concreteImplementation);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
