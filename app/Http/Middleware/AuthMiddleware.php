<?php

namespace App\Http\Middleware;

use App\Contracts\Services\AuthInterface;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authService = app()->make(AuthInterface::class);
        $user = $authService->authenticate();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
                'data' => null
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
