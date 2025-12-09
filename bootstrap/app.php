<?php

use App\Exceptions\CustomException;
use App\Http\Middleware\XRequestIdMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

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
            if ($th instanceof ValidationException) {
                Log::error('Validation failed', ['message' => $th->getMessage()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Something went wrong',
                    'data' => [
                        'errors' => $th->errors(),
                    ],
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            if ($th instanceof CustomException) {
                Log::error('Invalid request', ['message' => $th->getMessage()]);
                return response()->json([
                    'success' => false,
                    'message' => $th->getMessage(),
                    'data' => null,
                ], $th->getCode());
            }

            Log::error('Something went wrong', ['message' => $th->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'data' => null,
            ], $th->getCode() > 0 ? $th->getCode() : Response::HTTP_INTERNAL_SERVER_ERROR);
        });
    })->create();
