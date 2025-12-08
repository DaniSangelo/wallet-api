<?php

namespace App\Contracts\Services;

use App\DTO\UserCredentialsDTO;
use App\Models\User;

interface AuthInterface
{
    public function login(UserCredentialsDTO $credentials): string;
    public function logout(User $user): void;
    public function refresh(User $user): string;
    public function authenticate(): ?User;
    public function createToken(User $user): string;
}