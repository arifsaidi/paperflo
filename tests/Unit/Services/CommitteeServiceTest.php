<?php

use App\Models\ConferenceAdmin;
use App\Models\ConferenceAdminAssignment;
use App\Models\Paper;
use App\Models\Track;
use App\Services\CommitteeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    Mail::fake();

    // Seed roles
    DB::table('role')->insertOrIgnore([
        ['role_id' => 1, 'role_name' => 'SUPER_ADMIN', 'created_at' => now(), 'updated_at' => now()],
        ['role_id' => 2, 'role_name' => 'CONFERENCE_MANAGER', 'created_at' => now(), 'updated_at' => now()],
        ['role_id' => 3, 'role_name' => 'CONFERENCE_ADMIN', 'created_at' => now(), 'updated_at' => now()],
    ]);

    $this->conference = createTestConference([
        'name' => 'Committee Test Conf',
        'chairman_name' => 'Lead Organizer',
        'chairman_email' => 'organizer@conf.com',
        'conference_status' => 'active',
    ]);

    $this->service = app(CommitteeService::class);
});

test('isOrganizer identifies conference chairman by email and user ID', function () {
    $organizer = ConferenceAdmin::create([
        'name' => 'Lead Organizer',
        'email' => 'organizer@conf.com',
        'password' => bcrypt('secret'),
    ]);

    $otherUser = ConferenceAdmin::create([
        'name' => 'Regular Member',
        'email' => 'member@conf.com',
        'password' => bcrypt('secret'),
    ]);

    expect($this->service->isOrganizer($this->conference->conference_id, $organizer->admin_id))->toBeTrue();
    expect($this->service->isOrganizer($this->conference->conference_id, null, 'organizer@conf.com'))->toBeTrue();
    expect($this->service->isOrganizer($this->conference->conference_id, $otherUser->admin_id))->toBeFalse();
});

test('isManager identifies conference manager role accurately', function () {
    $manager = ConferenceAdmin::create([
        'name' => 'Manager User',
        'email' => 'manager@conf.com',
        'password' => bcrypt('secret'),
    ]);

    DB::table('user_role')->insert([
        'user_type' => 'conference_admin',
        'user_id' => $manager->admin_id,
        'role_id' => 2, // CONFERENCE_MANAGER
        'assigned_date' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $regularAdmin = ConferenceAdmin::create([
        'name' => 'Regular Admin',
        'email' => 'regular@conf.com',
        'password' => bcrypt('secret'),
    ]);

    expect($this->service->isManager($manager->admin_id))->toBeTrue();
    expect($this->service->isManager($regularAdmin->admin_id))->toBeFalse();
});

test('inviteAdmin creates admin account, assignment, and CONFERENCE_ADMIN role', function () {
    $this->service->inviteAdmin($this->conference->conference_id, 'newadmin@conf.com');

    $admin = ConferenceAdmin::where('email', 'newadmin@conf.com')->first();
    expect($admin)->not->toBeNull();

    $assigned = ConferenceAdminAssignment::where('conference_id', $this->conference->conference_id)
        ->where('admin_id', $admin->admin_id)
        ->exists();
    expect($assigned)->toBeTrue();

    $hasRole = DB::table('user_role')
        ->where('user_type', 'conference_admin')
        ->where('user_id', $admin->admin_id)
        ->where('role_id', 3)
        ->exists();
    expect($hasRole)->toBeTrue();
});

test('updateAccessLevel transfers manager role between admins', function () {
    // Current manager
    $currentManager = ConferenceAdmin::create([
        'name' => 'Current Manager',
        'email' => 'curr_mgr@conf.com',
        'password' => bcrypt('secret'),
    ]);
    ConferenceAdminAssignment::create([
        'conference_id' => $this->conference->conference_id,
        'admin_id' => $currentManager->admin_id,
        'assigned_date' => now(),
        'access_level' => 'manager',
    ]);
    DB::table('user_role')->insert([
        'user_type' => 'conference_admin',
        'user_id' => $currentManager->admin_id,
        'role_id' => 2, // CONFERENCE_MANAGER
        'assigned_date' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Target admin to be promoted
    $targetAdmin = ConferenceAdmin::create([
        'name' => 'Target Admin',
        'email' => 'target@conf.com',
        'password' => bcrypt('secret'),
    ]);
    ConferenceAdminAssignment::create([
        'conference_id' => $this->conference->conference_id,
        'admin_id' => $targetAdmin->admin_id,
        'assigned_date' => now(),
        'access_level' => 'admin',
    ]);
    DB::table('user_role')->insert([
        'user_type' => 'conference_admin',
        'user_id' => $targetAdmin->admin_id,
        'role_id' => 3, // CONFERENCE_ADMIN
        'assigned_date' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Promote target admin to manager
    $message = $this->service->updateAccessLevel(
        $this->conference->conference_id,
        $targetAdmin->admin_id,
        'manager',
        $currentManager->admin_id
    );

    expect($message)->toContain('You are now an Admin');

    // Verify target is now manager
    expect($this->service->isManager($targetAdmin->admin_id))->toBeTrue();
    // Verify previous manager is now regular admin
    expect($this->service->isManager($currentManager->admin_id))->toBeFalse();
});

test('removeAdmin deletes assignment from conference', function () {
    $admin = ConferenceAdmin::create([
        'name' => 'To Remove',
        'email' => 'remove@conf.com',
        'password' => bcrypt('secret'),
    ]);
    ConferenceAdminAssignment::create([
        'conference_id' => $this->conference->conference_id,
        'admin_id' => $admin->admin_id,
        'assigned_date' => now(),
        'access_level' => 'admin',
    ]);

    $removed = $this->service->removeAdmin($this->conference->conference_id, $admin->admin_id);

    expect($removed)->toBeTrue();
    expect(ConferenceAdminAssignment::where('conference_id', $this->conference->conference_id)
        ->where('admin_id', $admin->admin_id)->exists())->toBeFalse();
});

test('getDashboardStats aggregates counts accurately', function () {
    $track = Track::create([
        'conference_id' => $this->conference->conference_id,
        'name' => 'General Track',
    ]);

    Paper::create([
        'title' => 'Sample Paper 1',
        'conference_id' => $this->conference->conference_id,
        'track_id' => $track->track_id,
    ]);
    Paper::create([
        'title' => 'Sample Paper 2',
        'conference_id' => $this->conference->conference_id,
        'track_id' => $track->track_id,
    ]);

    $stats = $this->service->getDashboardStats($this->conference->conference_id);

    expect($stats['totalPapers'])->toBe(2);
    expect($stats['conference']->conference_id)->toBe($this->conference->conference_id);
});
