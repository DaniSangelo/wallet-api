<?php

use App\Models\User;
use App\Services\JwtAuthService;
use Illuminate\Support\Facades\Hash;

use function PHPUnit\Framework\assertArrayHasKey;

beforeEach(function () {
    $this->authService = new JwtAuthService();
    $this->user = User::create(['name' => fake()->name(), 'email' => 'example@mail.com', 'password' => Hash::make('password')]);
});

it('should be possible log in using valid credentials', function () {
    $response = $this->post('/api/v1/auth/login', [
        'email' => 'example@mail.com',
        'password' => 'password',
    ]);

    $response->assertStatus(200);
    assertArrayHasKey('access_token', $response['data']);
});

it('should not be possible log in with invalid credentials', function () {
    $response = $this->post('/api/v1/auth/login', [
        'email' => 'example@mail.com',
        'password' => 'skywalker',
    ]);
    $response->assertStatus(401);
});

it('should be possible logout', function() {
    $token = $this->authService->createToken($this->user);

    $response = $this->withHeaders([
        'Authorization' => 'Bearer '.$token,
    ])->post('/api/v1/auth/logout', []);
    $response->assertStatus(204);
});

it('should be possible to refresh token', function() {
    $token = $this->authService->createToken($this->user);
    $response = $this->withHeaders([
        'Authorization' => 'Bearer '.$token,
    ])->post('/api/v1/auth/refresh', []);
    $response->assertStatus(200);
    $content = json_decode($response->getContent(), true);
    $newToken = $content['data']['access_token'];
    $this->assertNotEquals($token, $newToken, 'Refresh token successful');
});