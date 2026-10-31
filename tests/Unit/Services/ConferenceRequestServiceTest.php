<?php

use App\Models\Conference;
use App\Models\ConferenceAdmin;
use App\Models\ConferenceRequest;
use App\Services\ConferenceRequestService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    Mail::fake();
    $this->service = app(ConferenceRequestService::class);
});

test('submitRequest saves a pending conference request', function () {
    $req = $this->service->submitRequest([
        'conference_title' => 'BioTech 2026',
        'org_name' => 'Bio Org',
        'org_type' => 'University',
        'org_address' => 'Bio Campus',
        'applicant_name' => 'Dr. Bio',
        'applicant_email' => 'bio@test.com',
        'applicant_phone' => '012345678',
    ]);

    expect($req->exists)->toBeTrue();
    expect($req->status)->toBe('Pending');
    expect($req->conference_title)->toBe('BioTech 2026');
});

test('approveRequest creates conference, admin, assignment and role', function () {
    // Ensure CONFERENCE_ADMIN role exists
    DB::table('role')->insertOrIgnore([
        'role_id' => 3,
        'role_name' => 'CONFERENCE_ADMIN',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $req = $this->service->submitRequest([
        'conference_title' => 'Robotics 2026',
        'org_name' => 'Robo Lab',
        'org_type' => 'Institute',
        'org_address' => 'Tokyo, Japan',
        'applicant_name' => 'Kenji Sato',
        'applicant_email' => 'kenji@robo.jp',
        'applicant_phone' => '987654321',
    ]);

    $result = $this->service->approveRequest($req->request_id);

    $req->refresh();
    expect($req->status)->toBe('Approved');

    // Conference created
    $conf = Conference::where('name', 'Robotics 2026')->first();
    expect($conf)->not->toBeNull();
    expect($conf->chairman_name)->toBe('Kenji Sato');
    expect($conf->chairman_email)->toBe('kenji@robo.jp');
    expect($conf->isDraft())->toBeTrue();

    // Admin created
    $admin = ConferenceAdmin::where('email', 'kenji@robo.jp')->first();
    expect($admin)->not->toBeNull();
    expect($admin->name)->toBe('Kenji Sato');

    // Admin assigned to conference
    expect($conf->admins->pluck('email')->toArray())->toContain('kenji@robo.jp');

    // Role assigned
    $hasRole = DB::table('user_role')
        ->where('user_type', 'conference_admin')
        ->where('user_id', $admin->admin_id)
        ->where('role_id', 3)
        ->exists();
    expect($hasRole)->toBeTrue();
});

test('rejectRequest marks conference request as rejected', function () {
    $req = $this->service->submitRequest([
        'conference_title' => 'Spam Conf',
        'org_name' => 'Spam Inc',
        'org_type' => 'Unknown',
        'org_address' => 'None',
        'applicant_name' => 'Spammer',
        'applicant_email' => 'spam@test.com',
        'applicant_phone' => '000',
    ]);

    $rejected = $this->service->rejectRequest($req->request_id);

    expect($rejected->status)->toBe('Rejected');
});
