<?php

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertOk()
        ->assertSee('Faisons connaissance.')
        ->assertSee('name="organization_name"', escape: false)
        ->assertSee('action="/register"', escape: false)
        ->assertSee('href="/login"', escape: false);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'organization_name' => 'Test Organization',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});
