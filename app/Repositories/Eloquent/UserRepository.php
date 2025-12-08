<?php

namespace App\Repositories\Eloquent;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\DTO\CreateUserDTO;
use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    public function create(CreateUserDTO $data): User
    {
        return User::create($data->toArray());
    }
}