<?php

namespace App\Services;

use App\Contracts\Services\AuthInterface;
use App\DTO\UserCredentialsDTO;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtAuthService implements AuthInterface
{
    public function login(UserCredentialsDTO $credentials): string
    {
        return Auth::attempt($credentials->toArray());
    }
    public function logout(User $user): void
    {
        JWTAuth::invalidate(JWTAuth::getToken());
    }
    public function refresh(User $user): string
    {
        return JWTAuth::refresh(JWTAuth::getToken());
    }
    public function authenticate(): ?User
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            Auth::setUser($user);
            return Auth::user();
        } catch (\Exception $e) {
            Log::error('Authentication failed', ['message' => $e->getMessage()]);
            return null;
        }
    }
    public function createToken(User $user): string
    {
        return JWTAuth::fromUser($user);
    }
}