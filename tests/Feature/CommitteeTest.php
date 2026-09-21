<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// Helper: build a committee admin with MANAGE_CONFERENCE permission
function committeeSession(int $conferenceId): array
{
    // Ensure MANAGE_CONFERENCE permission and role exist
    if (!DB::table('permission')->where('permission_name', 'MANAGE_CONFERENCE')->exists()) {
        DB::table('permission')->insert(['permission_name' => 'MANAGE_CONFERENCE', 'created_at' => now(), 'updated_at' => now()]);
    }

    $roleId = DB::table('role')->insertGetId([
        'role_name'  => 'CONFERENCE_ADMIN_' . uniqid(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $permId = DB::table('permission')->where('permission_name', 'MANAGE_CONFERENCE')->value('permission_id');
    DB::table('role_permission')->insert(['role_id' => $roleId, 'permission_id' => $permId, 'created_at' => now(), 'updated_at' => now()]);

    $adminId = DB::table('conference_admin')->insertGetId([
        'name'       => 'Committee Admin',
        'email'      => 'committee' . uniqid() . '@test.com',
        'password'   => Hash::make('pass'),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('conference_admin_assignment')->insert([
        'admin_id'      => $adminId,
        'conference_id' => $conferenceId,
        'access_level'  => 'admin',
        'assigned_date' => now(),
        'created_at'    => now(),
        'updated_at'    => now(),
    ]);

    DB::table('user_role')->insert([
        'user_type'     => 'conference_admin',
        'user_id'       => $adminId,
        'role_id'       => $roleId,
        'assigned_date' => now(),
        'created_at'    => now(),
        'updated_at'    => now(),
    ]);

    return ['user_id' => $adminId, 'user_type' => 'conference_admin'];
}

// Helper: create a draft conference
function createDraftConference(): int
{
    return DB::table('conference')->insertGetId([
        'name'                => 'Draft Conference',
        'conference_email'    => 'draft@test.com',
        'conference_phone'    => '0123456789',
        'secretariat_address' => 'Address',
        'chairman_name'       => 'Chair',
        'chairman_email'      => 'chair@test.com',
        'conference_date'     => now()->addMonths(6),
        'conference_venue'    => 'TBD',
        'conference_status'   => 'draft',
        'created_at'          => now(),
        'updated_at'          => now(),
    ]);
}

// --- Conference Setup ---

it('committee admin can view the conference setup page', function () {
    $conferenceId = createDraftConference();
    $session = committeeSession($conferenceId);

    $response = $this->withSession($session)
        ->get("/conference/{$conferenceId}/setup");

    $response->assertStatus(200);
});

it('guests cannot access conference setup', function () {
    $conferenceId = createDraftConference();

    $response = $this->get("/conference/{$conferenceId}/setup");
    $response->assertStatus(302);
});

// --- Tracks ---

it('committee admin can add a track to a conference', function () {
    $conferenceId = createDraftConference();
    $session = committeeSession($conferenceId);

    $response = $this->withSession($session)
        ->post("/conference/{$conferenceId}/tracks", [
            'name' => 'Machine Learning',
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('track', ['name' => 'Machine Learning', 'conference_id' => $conferenceId]);
});

// --- Deadlines ---

it('committee admin can update conference deadlines', function () {
    $conferenceId = createDraftConference();
    $session = committeeSession($conferenceId);

    // Ensure a deadline row exists
    DB::table('deadline')->insert([
        'conference_id'    => $conferenceId,
        'abstract_deadline' => null,
        'created_at'       => now(),
        'updated_at'       => now(),
    ]);

    $response = $this->withSession($session)
        ->post("/conference/{$conferenceId}/deadlines", [
            'abstract_deadline' => now()->addDays(30)->format('Y-m-d'),
        ]);

    $response->assertRedirect();
});

// --- Dashboard ---

it('committee admin can view the committee dashboard', function () {
    $conferenceId = createDraftConference();
    $session = committeeSession($conferenceId);

    $response = $this->withSession($session)
        ->get("/conference/{$conferenceId}/committee/dashboard");

    $response->assertStatus(200);
});

// --- Participants ---

it('committee admin can list participants', function () {
    $conferenceId = createDraftConference();
    $session = committeeSession($conferenceId);

    $response = $this->withSession($session)
        ->get("/conference/{$conferenceId}/participants");

    $response->assertStatus(200);
});

// --- Payments ---

it('committee admin can view payment list', function () {
    $conferenceId = createDraftConference();
    $session = committeeSession($conferenceId);

    $response = $this->withSession($session)
        ->get("/conference/{$conferenceId}/payments");

    $response->assertStatus(200);
});
