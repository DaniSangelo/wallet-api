<?php

namespace App\Http\Controllers;

use App\Contracts\Lib\Encryptor;
use App\Contracts\Services\AuthInterface;
use App\DTO\CreateUserDTO;
use App\Http\Requests\CreateUserRequest;
use App\Services\UserService;
use Illuminate\Support\Facades\Log;

class UserController extends BaseController
{
    public Encryptor $encryptor;
    public UserService $userService;
    public AuthInterface $authService;

    public function __construct(Encryptor $encryptor, UserService $userService, AuthInterface $authService)
    {
        $this->encryptor = $encryptor;
        $this->userService = $userService;
        $this->authService = $authService;
    }

    public function create(CreateUserRequest $request)
    {
        Log::info('Start - registering a new user', ['name' => $request->name, 'email' => $request->email]);

        $createUserDto = CreateUserDTO::createFromArray($request->validated());
        $createUserDto->password = $this->encryptor->encrypt($createUserDto->password);
        $user = $this->userService->create($createUserDto);

        Log::info('End - registering a new user');

        $token = $this->authService->createToken($user);

        return $this->success('User registered successfully', [
            'success' => true,
            'message' => 'User registered successfully',
            'data' => ['access_token' => $token],
        ]);
    }
}
