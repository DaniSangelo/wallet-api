<?php

namespace App\Http\Controllers;

use App\Contracts\Services\AuthInterface;
use App\DTO\UserCredentialsDTO;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;

class AuthController extends BaseController
{
    public AuthInterface $authService;

    public function __construct(AuthInterface $authService)
    {
        $this->authService = $authService;
    }

    public function login(LoginRequest $request)
    {
        $data = $request->validated();
        $credentials = UserCredentialsDTO::createFromArray($data);
        $token = $this->authService->login($credentials);
        return $this->success('Login successful', ['success' => true, 'message' => 'Login successful', 'data' => ['access_token' => $token]]);
    }

    public function logout() 
    {
        
    }
}
