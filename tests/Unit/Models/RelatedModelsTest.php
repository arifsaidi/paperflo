<?php

use App\Models\ConferenceAdmin;
use App\Models\ConferenceAdminAssignment;
use App\Models\ConferenceFee;
use App\Models\ConferenceRegistration;
use App\Models\ConferenceRequest;
use App\Models\Deadline;
use App\Models\Paper;
use App\Models\Track;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->conference = createTestConference([
        'name' => 'Tech Summit 2026',
        'conference_status' => 'active',
    ]);
});

test('track model belongs to conference and can have papers', function () {
    $track = Track::create([
        'name' => 'Data Science',
        'conference_id' => $this->conference->conference_id,
    ]);

    $paper = Paper::create([
        'title' => 'Deep Learning in Health',
        'conference_id' => $this->conference->conference_id,
        'track_id' => $track->track_id,
        'keywords' => 'AI, Health',
    ]);

    expect($track->conference->name)->toBe('Tech Summit 2026');
    expect($track->papers)->toHaveCount(1);
    expect($track->papers->first()->title)->toBe('Deep Learning in Health');
});

test('deadline model casts all 7 deadline dates to Carbon instances', function () {
    $deadline = Deadline::create([
        'conference_id' => $this->conference->conference_id,
        'abstract_deadline' => '2026-06-01 23:59:59',
        'fullpaper_deadline' => '2026-07-01 23:59:59',
        'cameraready_paper_deadline' => '2026-08-01 23:59:59',
        'participant_registration_deadline' => '2026-09-01 23:59:59',
        'review_deadline' => '2026-07-15 23:59:59',
        'listener_payment_deadline' => '2026-09-15 23:59:59',
        'presenter_payment_deadline' => '2026-08-15 23:59:59',
    ]);

    expect($deadline->abstract_deadline)->toBeInstanceOf(Carbon::class);
    expect($deadline->fullpaper_deadline)->toBeInstanceOf(Carbon::class);
    expect($deadline->cameraready_paper_deadline)->toBeInstanceOf(Carbon::class);
    expect($deadline->participant_registration_deadline)->toBeInstanceOf(Carbon::class);
    expect($deadline->review_deadline)->toBeInstanceOf(Carbon::class);
    expect($deadline->listener_payment_deadline)->toBeInstanceOf(Carbon::class);
    expect($deadline->presenter_payment_deadline)->toBeInstanceOf(Carbon::class);
    expect($deadline->conference->conference_id)->toBe($this->conference->conference_id);
});

test('conference fee model casts amount to decimal string and belongs to conference', function () {
    $fee = ConferenceFee::create([
        'conference_id' => $this->conference->conference_id,
        'name' => 'Student Early Bird',
        'amount' => 250.50,
        'currency' => 'MYR',
    ]);

    expect($fee->amount)->toBe('250.50');
    expect($fee->currency)->toBe('MYR');
    expect($fee->conference->name)->toBe('Tech Summit 2026');
});

test('conference registration model records attendee details and casts dates', function () {
    $participantId = createTestParticipant();

    $reg = ConferenceRegistration::create([
        'conference_id' => $this->conference->conference_id,
        'participant_id' => $participantId,
        'registration_type' => 'author',
        'registration_category' => 'Presenter Fee',
        'registration_status' => 'registered',
        'registration_date' => now(),
    ]);

    expect($reg->registration_type)->toBe('author');
    expect($reg->registration_date)->toBeInstanceOf(Carbon::class);
    expect($reg->conference->conference_id)->toBe($this->conference->conference_id);
});

test('conference admin model protects hidden password and relates to conferences', function () {
    $admin = ConferenceAdmin::create([
        'name' => 'John Committee',
        'email' => 'john@committee.org',
        'password' => bcrypt('password123'),
    ]);

    expect(array_key_exists('password', $admin->toArray()))->toBeFalse();

    ConferenceAdminAssignment::create([
        'conference_id' => $this->conference->conference_id,
        'admin_id' => $admin->admin_id,
        'assigned_date' => now(),
        'access_level' => 'admin',
    ]);

    expect($admin->conferences)->toHaveCount(1);
    expect($admin->conferences->first()->name)->toBe('Tech Summit 2026');
});

test('conference request model scopes query pending, approved, and rejected', function () {
    $participantId = createTestParticipant();
    $superadminId = createTestSuperAdmin();

    ConferenceRequest::create([
        'conference_title' => 'Pending Conf',
        'org_name' => 'Org A',
        'org_type' => 'University',
        'org_address' => 'Addr A',
        'applicant_name' => 'App A',
        'applicant_email' => 'a@test.com',
        'applicant_phone' => '111',
        'participant_id' => $participantId,
        'superadmin_id' => $superadminId,
        'status' => 'Pending',
        'submitted_date' => now(),
    ]);

    ConferenceRequest::create([
        'conference_title' => 'Approved Conf',
        'org_name' => 'Org B',
        'org_type' => 'Institute',
        'org_address' => 'Addr B',
        'applicant_name' => 'App B',
        'applicant_email' => 'b@test.com',
        'applicant_phone' => '222',
        'participant_id' => $participantId,
        'superadmin_id' => $superadminId,
        'status' => 'Approved',
        'submitted_date' => now(),
    ]);

    ConferenceRequest::create([
        'conference_title' => 'Rejected Conf',
        'org_name' => 'Org C',
        'org_type' => 'Company',
        'org_address' => 'Addr C',
        'applicant_name' => 'App C',
        'applicant_email' => 'c@test.com',
        'applicant_phone' => '333',
        'participant_id' => $participantId,
        'superadmin_id' => $superadminId,
        'status' => 'Rejected',
        'submitted_date' => now(),
    ]);

    expect(ConferenceRequest::pending()->count())->toBe(1);
    expect(ConferenceRequest::approved()->count())->toBe(1);
    expect(ConferenceRequest::rejected()->count())->toBe(1);
});
