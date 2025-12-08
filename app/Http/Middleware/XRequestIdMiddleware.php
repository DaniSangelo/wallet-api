<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class XRequestIdMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $requestId = $request->header('x-request-id') ?? Str::uuid()->toString();
        $request->headers->set('x-request-id', $requestId);
        Context::add('x-request-id', $requestId);
        $response = $next($request);
        $response->headers->set('x-request-id', $requestId);
        return $response;
    }
}
