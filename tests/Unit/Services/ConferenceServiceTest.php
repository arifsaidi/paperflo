<?php

use App\Models\Conference;
use App\Models\ConferenceFee;
use App\Models\ConferenceRegistration;
use App\Models\Deadline;
use App\Models\Track;
use App\Services\ConferenceService;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->service = app(ConferenceService::class);
});

test('getConference returns conference or null', function () {
    $conf = createTestConference(['name' => 'Sample Conf']);

    expect($this->service->getConference($conf->conference_id))->not->toBeNull();
    expect($this->service->getConference(99999))->toBeNull();
});

test('findConferenceOrFail returns conference or throws 404', function () {
    $conf = createTestConference(['name' => 'Sample Conf']);

    expect($this->service->findConferenceOrFail($conf->conference_id)->name)->toBe('Sample Conf');
});

test('getActiveConferences returns only active conferences ordered by date', function () {
    createTestConference([
        'name' => 'Draft Conf',
        'conference_date' => now()->addMonth(),
        'conference_status' => 'draft',
    ]);

    $active2 = createTestConference([
        'name' => 'Active Conf Later',
        'conference_date' => now()->addMonths(5),
        'conference_status' => 'active',
    ]);

    $active1 = createTestConference([
        'name' => 'Active Conf Sooner',
        'conference_date' => now()->addMonths(2),
        'conference_status' => 'active',
    ]);

    $activeConfs = $this->service->getActiveConferences();

    expect($activeConfs)->toHaveCount(2);
    expect($activeConfs->first()->name)->toBe('Active Conf Sooner');
    expect($activeConfs->last()->name)->toBe('Active Conf Later');
});

test('getConferenceDetail loads full details including fees, tracks, deadlines and registration', function () {
    $conf = createTestConference([
        'name' => 'Full Detail Conf',
        'conference_status' => 'active',
    ]);

    Track::create(['name' => 'Track A', 'conference_id' => $conf->conference_id]);
    ConferenceFee::create(['name' => 'Fee A', 'amount' => 100, 'conference_id' => $conf->conference_id]);
    Deadline::create([
        'conference_id' => $conf->conference_id,
        'abstract_deadline' => now()->addMonth(),
        'fullpaper_deadline' => now()->addMonth(),
        'cameraready_paper_deadline' => now()->addMonth(),
        'participant_registration_deadline' => now()->addMonth(),
        'review_deadline' => now()->addMonth(),
        'listener_payment_deadline' => now()->addMonth(),
        'presenter_payment_deadline' => now()->addMonth(),
    ]);

    $participantId = createTestParticipant();

    ConferenceRegistration::create([
        'conference_id' => $conf->conference_id,
        'participant_id' => $participantId,
        'registration_type' => 'listener',
        'registration_category' => 'Fee A',
        'registration_status' => 'registered',
        'registration_date' => now(),
    ]);

    $details = $this->service->getConferenceDetail($conf->conference_id, $participantId);

    expect($details['conference']->name)->toBe('Full Detail Conf');
    expect($details['tracks'])->toHaveCount(1);
    expect($details['fees'])->toHaveCount(1);
    expect($details['deadlines'])->not->toBeNull();
    expect($details['registration'])->not->toBeNull();
    expect($details['registration']->registration_type)->toBe('listener');
});

test('updateConferenceInfo updates allowed fields and rejects closed conference', function () {
    $conf = createTestConference([
        'name' => 'Original Name',
        'chairman_name' => 'Old Chair',
        'conference_status' => 'draft',
    ]);

    $updated = $this->service->updateConferenceInfo($conf->conference_id, [
        'name' => 'New Name',
        'chairman_name' => 'New Chair',
        'conference_email' => 'new@conf.com',
        'conference_phone' => '222',
        'secretariat_address' => 'New Addr',
    ]);

    expect($updated->name)->toBe('New Name');
    expect($updated->chairman_name)->toBe('New Chair');

    // Reject update on closed conference
    $updated->update(['conference_status' => 'closed']);
    expect(fn () => $this->service->updateConferenceInfo($conf->conference_id, ['name' => 'Fail']))
        ->toThrow(InvalidArgumentException::class, 'Conference is closed');
});

test('activateConference succeeds when required fields exist and fails when missing', function () {
    $draft = createTestConference([
        'name' => 'Incomplete Conf',
        'chairman_name' => null,
        'chairman_email' => null,
        'conference_venue' => null,
        'conference_status' => 'draft',
    ]);

    // Fails because required fields (chairman_name, chairman_email, conference_date, conference_venue) are empty
    expect(fn () => $this->service->activateConference($draft->conference_id))
        ->toThrow(InvalidArgumentException::class, 'Missing required fields');

    // Supply required fields
    $draft->update([
        'chairman_name' => 'Valid Chair',
        'chairman_email' => 'chair@conf.com',
        'conference_date' => now()->addMonths(3),
        'conference_venue' => 'Main Auditorium',
    ]);

    $activated = $this->service->activateConference($draft->conference_id);

    expect($activated->conference_status)->toBe('active');
    expect($activated->isActive())->toBeTrue();
});
