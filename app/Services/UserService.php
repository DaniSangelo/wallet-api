<?php

namespace App\Services;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\DTO\CreateUserDTO;

class UserService
{
    public UserRepositoryInterface $userRepository;
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function create(CreateUserDTO $data)
    {
        return $this->userRepository->create($data);
    }
}