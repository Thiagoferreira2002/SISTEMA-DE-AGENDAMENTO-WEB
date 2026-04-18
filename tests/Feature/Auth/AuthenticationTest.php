<?php

use App\Models\User;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertOk();
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create([
        'cpf' => '12345678901',
        'status' => 'ativo',
        'nivel' => 'user',
    ]);

    $response = $this->post('/login', [
        'cpf' => $user->cpf,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('cliente.dashboard', absolute: false));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create([
        'cpf' => '12345678902',
        'status' => 'ativo',
    ]);

    $this->post('/login', [
        'cpf' => $user->cpf,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});
