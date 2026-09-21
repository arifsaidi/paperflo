<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// Helper
function participantSessionForProfile(): array
{
    $id = DB::table('participant')->insertGetId([
        'name'              => 'Profile User',
        'email'             => 'profile@test.com',
        'password'          => Hash::make('oldpassword'),
        'institution'       => 'UTM',
        'registration_date' => now(),
        'created_at'        => now(),
        'updated_at'        => now(),
    ]);

    return ['user_id' => $id, 'user_type' => 'participant', '_id' => $id];
}

// --- View Profile ---

it('participant can view their profile', function () {
    $session = participantSessionForProfile();

    $response = $this->withSession($session)->get('/participant/profile');
    $response->assertStatus(200);
});

it('guest cannot view participant profile', function () {
    $response = $this->get('/participant/profile');
    $response->assertStatus(302);
});

// --- Update Profile ---

it('participant can update their profile', function () {
    $session = participantSessionForProfile();

    $response = $this->withSession($session)->post('/participant/profile', [
        'name'        => 'Updated Name',
        'institution' => 'Stanford',
        'faculty'     => 'Engineering',
        'department'  => 'CS',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('participant', ['participant_id' => $session['_id'], 'name' => 'Updated Name']);
});

// --- Change Password ---

it('participant can change their password', function () {
    $session = participantSessionForProfile();

    $response = $this->withSession($session)->post('/participant/profile/password', [
        'current_password'      => 'oldpassword',
        'password'              => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    $response->assertRedirect();

    $updated = DB::table('participant')->where('participant_id', $session['_id'])->first();
    $this->assertTrue(Hash::check('newpassword123', $updated->password));
});

it('rejects password change with wrong current password', function () {
    $session = participantSessionForProfile();

    $response = $this->withSession($session)->post('/participant/profile/password', [
        'current_password'      => 'wrongpassword',
        'password'              => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    $response->assertSessionHasErrors();
});
