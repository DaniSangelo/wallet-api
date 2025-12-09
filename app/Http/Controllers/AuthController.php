<?php

namespace App\Http\Controllers;

use App\Contracts\Services\AuthInterface;
use App\DTO\UserCredentialsDTO;
use App\Exceptions\CustomException;
use App\Http\Requests\LoginRequest;
use Exception;
use Illuminate\Support\Facades\Log;
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
        try {
            $data = $request->validated();
            $credentials = UserCredentialsDTO::createFromArray($data);
            $user = $this->authService->login($credentials);
            $token = $this->authService->createToken($user);
            return $this->success('Login successful', ['success' => true, 'message' => 'Login successful', 'data' => ['access_token' => $token]]);
        } catch (CustomException $e) {
            Log::error('Login failed', ['message' => $e->getMessage()]);
            return $this->error('Login failed', ['success' => false, 'message' => 'Unauthorized', 'data' => null], $e->getCode());
        } catch (Exception $e) {
            Log::error('Login failed', ['message' => $e->getMessage()]);
            return $this->error('Login failed', ['success' => false, 'message' => 'Login failed', 'data' => null]);
        }
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
