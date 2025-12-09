<?php

namespace App\Http\Controllers;

use App\Contracts\Services\AuthInterface;
use App\DTO\UserCredentialsDTO;
use App\Http\Requests\LoginRequest;
use Symfony\Component\HttpFoundation\Response;

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
        $user = $this->authService->login($credentials);
        $token = $this->authService->createToken($user);
        return $this->success('Login successful', ['success' => true, 'message' => 'Login successful', 'data' => ['access_token' => $token]]);
    }

    public function logout() 
    {
        $this->authService->logout(auth()->user());
        return $this->success('Logout successful', [], Response::HTTP_NO_CONTENT);
    }

    public function refresh()
    {
        $token = $this->authService->refresh(auth()->user());
        return $this->success('Refresh token successful', ['success' => true, 'message' => 'Refresh token successful', 'data' => ['access_token' => $token]]);
    }
}
