<?php

use function PHPUnit\Framework\assertTrue;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();

    assertTrue(auth()->user()->organisations()->count() === 1);
    assertTrue(auth()->user()->ownedOrganisations()->count() === 1);

    $response->assertRedirect(route('dashboard', absolute: false));
});