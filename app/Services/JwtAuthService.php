<?php

namespace App\Services;

use App\Contracts\Services\AuthInterface;
use App\DTO\UserCredentialsDTO;
use App\Exceptions\CustomException;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtAuthService implements AuthInterface
{
    public function login(UserCredentialsDTO $credentials): User|bool
    {
        if (JWTAuth::attempt($credentials->toArray())) {
            return JWTAuth::user();
        };

        throw new CustomException('Invalid credentials', Response::HTTP_UNAUTHORIZED);
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
            if (!$user) return null;
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