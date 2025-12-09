<?php

it('should be possible to add a new user', function () {
    $response = $this->post('/api/v1/users', [
        'name' => 'daniel',
        'email' => 'email@mail.com',
        'password' => 'password',
    ]);

    $response->assertStatus(200);
    $this->assertArrayHasKey('access_token', $response['data']);
});

it('should not be possible create a user without password', function() {
    $response = $this->post('/api/v1/users', [
        'name' => 'daniel',
        'email' => 'email@mail.com',
    ]);

    $response->assertStatus(422);
});

it('should not be possible create a user with an invalid email', function() {
    $response = $this->post('/api/v1/users', [
        'name' => 'daniel',
        'email' => 'email',
    ]);

    $response->assertStatus(422);
});