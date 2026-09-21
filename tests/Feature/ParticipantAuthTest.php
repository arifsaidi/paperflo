<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// --- Register Form ---

it('shows the participant registration page', function () {
    $response = $this->get('/register');
    $response->assertStatus(200);
});

// --- Successful Registration ---

it('registers a new participant successfully', function () {
    $response = $this->post('/register', [
        'name'                  => 'Jane Doe',
        'email'                 => 'jane@test.com',
        'password'              => 'password123',
        'password_confirmation' => 'password123',
        'institution'           => 'UTM',
        'faculty'               => 'Computing',
        'department'            => 'Software Engineering',
    ]);

    $response->assertRedirect(route('participant.conferences'));

    $this->assertDatabaseHas('participant', ['email' => 'jane@test.com']);
});

it('auto-logs in the participant after registration', function () {
    $this->post('/register', [
        'name'                  => 'Auto Login',
        'email'                 => 'autologin@test.com',
        'password'              => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $this->assertEquals('participant', session('user_type'));
});

// --- Validation ---

it('requires name field on registration', function () {
    $response = $this->post('/register', [
        'email'                 => 'test@test.com',
        'password'              => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors('name');
});

it('requires a unique email on registration', function () {
    DB::table('participant')->insert([
        'name'              => 'Existing User',
        'email'             => 'taken@test.com',
        'password'          => Hash::make('pass'),
        'registration_date' => now(),
        'created_at'        => now(),
        'updated_at'        => now(),
    ]);

    $response = $this->post('/register', [
        'name'                  => 'New User',
        'email'                 => 'taken@test.com',
        'password'              => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertSessionHasErrors('email');
});

it('requires password confirmation to match', function () {
    $response = $this->post('/register', [
        'name'                  => 'Mismatch',
        'email'                 => 'mismatch@test.com',
        'password'              => 'password123',
        'password_confirmation' => 'differentpassword',
    ]);

    $response->assertSessionHasErrors('password');
});

it('requires minimum password length of 6', function () {
    $response = $this->post('/register', [
        'name'                  => 'Short Pass',
        'email'                 => 'short@test.com',
        'password'              => '123',
        'password_confirmation' => '123',
    ]);

    $response->assertSessionHasErrors('password');
});
