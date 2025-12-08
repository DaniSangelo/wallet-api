<?php

namespace App\Contracts\Repositories;

use App\DTO\CreateUserDTO;
use App\Models\User;

interface UserRepositoryInterface
{
    public function create(CreateUserDTO $userDto): User;
    public function getByEmail(string $email): ?User;
}