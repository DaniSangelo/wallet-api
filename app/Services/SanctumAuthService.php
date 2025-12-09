<?php

namespace App\Services;

use App\Contracts\Services\AuthInterface;
use App\DTO\UserCredentialsDTO;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SanctumAuthService implements AuthInterface
{
    public function login(UserCredentialsDTO $credentials): User|bool
    {
        $user = Auth::attempt($credentials->toArray());
        if ($user) return Auth::user();
        return false;
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    public function refresh(User $user): string
    {
        $user->currentAccessToken()->delete();
        return $this->createToken($user);
    }

    public function authenticate(): ?User
    {
        try {
            $user = Auth::guard('sanctum')->user();
            if (!$user) return null;
            Auth::setUser($user);
            return $user;
        } catch (Exception $e) {
            Log::error('Error on sanctum authentication', ['message' => $e->getMessage()]);
            return null;
        }
    }

    public function createToken(User $user): string
    {
        return $user->createToken('access_token')->plainTextToken;
    }
}