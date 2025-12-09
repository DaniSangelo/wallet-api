<?php

use App\Models\User;
use App\Services\JwtAuthService;

beforeEach(function () {
    $this->authService = new JwtAuthService();
    $this->user = User::create(['name' => fake()->name(), 'email' => 'example@mail.com', 'password' => Hash::make('password')]);
});

it('should not be possible to add a wallet without be loged in', function () {
    $response = $this->post('/api/v1/users/wallet', [
        'account' => (string) rand(12342, 60493),
    ]);

    $response->assertStatus(401);
});

it('should not be possible to add a wallet without an account', function () {
    $token = $this->authService->createToken($this->user);

    $response = $this
        ->withHeaders(['Authorization' => 'Bearer '.$token])
        ->post('/api/v1/users/wallet');

    $response->assertStatus(422);
});

it('should be possible to add a wallet', function () {
    $token = $this->authService->createToken($this->user);

    $response = $this
        ->withHeaders(['Authorization' => 'Bearer '.$token])
        ->post('/api/v1/users/wallet', ['account' => (string) rand(12342, 60493)]);

    $response->assertStatus(200);
});

it('should be possible get the wallet balance', function () {
    $response = $this->post('/api/v1/users', [
        'name' => 'daniel',
        'email' => 'email@mail.com',
        'password' => 'password',
    ]);
    $content = json_decode($response->getContent(), true);
    $token = $content['data']['access_token'];

    $response = $this
        ->withHeaders(['Authorization' => 'Bearer '.$token])
        ->post('/api/v1/users/wallet', ['account' => (string) rand(12342, 60493)]);

    $response = $this
        ->withHeaders(['Authorization' => 'Bearer '.$token])
        ->get('/api/v1/users/wallet/balance');

    $response->assertStatus(200);
    $this->assertArrayHasKey('balance', $response['data']);
});
