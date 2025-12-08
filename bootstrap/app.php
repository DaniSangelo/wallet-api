<?php

use App\Http\Middleware\XRequestIdMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        apiPrefix: '/api/v1',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(XRequestIdMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $th) {
            Log::error('Something went wrong', ['message' => $th->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'data' => null,
            ]);
        });
    })->create();
