<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// Helper: insert a participant and return their id
function createParticipant(string $email, string $password): int
{
    return DB::table('participant')->insertGetId([
        'name'              => 'Test User',
        'email'             => $email,
        'password'          => Hash::make($password),
        'registration_date' => now(),
        'created_at'        => now(),
        'updated_at'        => now(),
    ]);
}

// Helper: insert a super admin and return their id
function createSuperAdmin(string $email, string $password): int
{
    return DB::table('super_admin')->insertGetId([
        'name'       => 'Super Admin',
        'email'      => $email,
        'password'   => Hash::make($password),
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

// --- Login Form ---

it('shows the login page', function () {
    $response = $this->get('/login');
    $response->assertStatus(200);
});

// --- Valid Login ---

it('logs in a participant with correct credentials', function () {
    createParticipant('participant@test.com', 'password123');

    $response = $this->post('/login', [
        'email'    => 'participant@test.com',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('home'));
    $this->assertEquals('participant', session('user_type'));
});

it('logs in a super admin with correct credentials', function () {
    createSuperAdmin('admin@test.com', 'adminpass');

    $response = $this->post('/login', [
        'email'    => 'admin@test.com',
        'password' => 'adminpass',
    ]);

    $response->assertRedirect(route('conference.request.index'));
    $this->assertEquals('super_admin', session('user_type'));
});

// --- Invalid Login ---

it('rejects login with wrong password', function () {
    createParticipant('wrong@test.com', 'rightpassword');

    $response = $this->post('/login', [
        'email'    => 'wrong@test.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertSessionHas('error', 'Invalid email or password.');
});

it('rejects login with non-existent email', function () {
    $response = $this->post('/login', [
        'email'    => 'nobody@test.com',
        'password' => 'anything',
    ]);

    $response->assertSessionHas('error', 'Invalid email or password.');
});

// --- Validation ---

it('requires email field', function () {
    $response = $this->post('/login', [
        'password' => 'password123',
    ]);

    $response->assertSessionHasErrors('email');
});

it('requires password field', function () {
    $response = $this->post('/login', [
        'email' => 'test@test.com',
    ]);

    $response->assertSessionHasErrors('password');
});

it('requires a valid email format', function () {
    $response = $this->post('/login', [
        'email'    => 'not-an-email',
        'password' => 'password123',
    ]);

    $response->assertSessionHasErrors('email');
});

// --- Rate Limiting ---

it('blocks after 5 failed login attempts', function () {
    for ($i = 0; $i < 6; $i++) {
        $response = $this->post('/login', [
            'email'    => 'brute@test.com',
            'password' => 'wrongpassword',
        ]);
    }

    $response->assertStatus(429);
});

// --- Logout ---

it('logs out and invalidates the session', function () {
    createParticipant('logout@test.com', 'password123');

    $this->withSession([
        'user_id'   => 1,
        'user_type' => 'participant',
    ]);

    $response = $this->post('/logout');
    $response->assertRedirect(route('login.form'));
    $response->assertSessionHas('success', 'Logged out.');
});
