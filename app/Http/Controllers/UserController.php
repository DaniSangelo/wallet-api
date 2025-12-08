<?php

namespace App\Http\Controllers;

use App\Contracts\Lib\Encryptor;
use App\DTO\CreateUserDTO;
use App\Http\Requests\CreateUserRequest;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends BaseController
{
    public Encryptor $encryptor;
    public UserService $userService;

    public function __construct(Encryptor $encryptor, UserService $userService)
    {
        $this->encryptor = $encryptor;
        $this->userService = $userService;
    }

    public function create(CreateUserRequest $request)
    {
        Log::info('Start - registering a new user', ['name' => $request->name, 'email' => $request->email]);

        $createUserDto = CreateUserDTO::createFromArray($request->validated());
        $createUserDto->password = $this->encryptor->encrypt($createUserDto->password);
        $user = $this->userService->create($createUserDto);

        Log::info('End - registering a new user');

        return $this->success('User registered successfully', [
            'success' => true,
            'message' => 'User registered successfully',
            //todo: return access_token on user creation
            'data' => null,
        ]);
    }
}
