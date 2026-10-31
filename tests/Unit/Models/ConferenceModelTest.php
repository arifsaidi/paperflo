<?php

use App\Models\Conference;
use App\Models\ConferenceAdmin;
use App\Models\ConferenceAdminAssignment;
use App\Models\ConferenceFee;
use App\Models\Deadline;
use App\Models\Track;
use Carbon\Carbon;

test('conference model has correct attributes and fillables', function () {
    $conf = createTestConference([
        'name' => 'International AI Summit 2026',
        'logo' => 'uploads/logos/ai_summit.png',
        'chairman_name' => 'Dr. Alan Turing',
        'chairman_email' => 'turing@ai.org',
        'publication' => 'IEEE',
        'conference_venue' => 'Grand Hall',
        'contents' => 'Premier conference on AI systems',
    ]);

    expect($conf->exists)->toBeTrue();
    expect($conf->conference_id)->not->toBeNull();
    expect($conf->name)->toBe('International AI Summit 2026');
    expect($conf->chairman_name)->toBe('Dr. Alan Turing');
    expect($conf->conference_date)->toBeInstanceOf(Carbon::class);
});

test('conference attribute aliases and accessors work correctly', function () {
    $conf = new Conference([
        'name' => 'WebTech 2026',
        'conference_status' => 'active',
        'chairman_name' => 'Tim Berners-Lee',
        'contents' => 'All about the web',
        'logo' => 'uploads/logos/web.png',
    ]);

    // Test getters
    expect($conf->title)->toBe('WebTech 2026');
    expect($conf->status)->toBe('active');
    expect($conf->chairperson)->toBe('Tim Berners-Lee');
    expect($conf->about)->toBe('All about the web');
    expect($conf->theme)->toBe('All about the web');
    expect($conf->logo_url)->toContain('uploads/logos/web.png');

    // Test setters
    $conf->title = 'WebTech Global 2026';
    expect($conf->name)->toBe('WebTech Global 2026');

    $conf->status = 'closed';
    expect($conf->conference_status)->toBe('closed');

    $conf->chairperson = 'Sir Tim';
    expect($conf->chairman_name)->toBe('Sir Tim');

    $conf->about = 'New web trends';
    expect($conf->contents)->toBe('New web trends');
});

test('conference status helpers return accurate booleans', function () {
    $draft = new Conference(['conference_status' => 'draft']);
    expect($draft->isDraft())->toBeTrue();
    expect($draft->isActive())->toBeFalse();
    expect($draft->isClosed())->toBeFalse();

    $active = new Conference(['conference_status' => 'active']);
    expect($active->isDraft())->toBeFalse();
    expect($active->isActive())->toBeTrue();
    expect($active->isClosed())->toBeFalse();

    $closed = new Conference(['conference_status' => 'closed']);
    expect($closed->isDraft())->toBeFalse();
    expect($closed->isActive())->toBeFalse();
    expect($closed->isClosed())->toBeTrue();
});

test('conference scopes filter query results by status', function () {
    createTestConference(['name' => 'Conf 1', 'conference_status' => 'draft']);
    createTestConference(['name' => 'Conf 2', 'conference_status' => 'active']);
    createTestConference(['name' => 'Conf 3', 'conference_status' => 'closed']);

    expect(Conference::draft()->count())->toBe(1);
    expect(Conference::active()->count())->toBe(1);
    expect(Conference::closed()->count())->toBe(1);
});

test('conference relationships load properly', function () {
    $conf = createTestConference(['name' => 'Relationship Conf', 'conference_status' => 'active']);

    // Track
    $track = Track::create([
        'name' => 'Cybersecurity',
        'conference_id' => $conf->conference_id,
    ]);

    // Deadline
    $deadline = Deadline::create([
        'conference_id' => $conf->conference_id,
        'abstract_deadline' => now()->addMonth(),
        'fullpaper_deadline' => now()->addMonths(2),
        'cameraready_paper_deadline' => now()->addMonths(3),
        'participant_registration_deadline' => now()->addMonths(4),
        'review_deadline' => now()->addMonths(2),
        'listener_payment_deadline' => now()->addMonths(4),
        'presenter_payment_deadline' => now()->addMonths(4),
    ]);

    // Fee
    $fee = ConferenceFee::create([
        'conference_id' => $conf->conference_id,
        'name' => 'Regular Author',
        'amount' => 450.00,
        'currency' => 'MYR',
    ]);

    // Admin & Assignment
    $admin = ConferenceAdmin::create([
        'name' => 'Admin Jane',
        'email' => 'jane@test.com',
        'password' => bcrypt('secret'),
    ]);

    ConferenceAdminAssignment::create([
        'conference_id' => $conf->conference_id,
        'admin_id' => $admin->admin_id,
        'assigned_date' => now(),
        'access_level' => 'admin',
    ]);

    // Reload with relations
    $conf->refresh();

    expect($conf->tracks)->toHaveCount(1);
    expect($conf->tracks->first()->name)->toBe('Cybersecurity');

    expect($conf->deadline)->not->toBeNull();
    expect($conf->deadline->deadline_id)->toBe($deadline->deadline_id);

    expect($conf->fees)->toHaveCount(1);
    expect((float) $conf->fees->first()->amount)->toBe(450.0);

    expect($conf->admins)->toHaveCount(1);
    expect($conf->admins->first()->name)->toBe('Admin Jane');
    expect($conf->admins->first()->pivot->access_level)->toBe('admin');
});
