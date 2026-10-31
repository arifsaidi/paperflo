<?php

use App\Models\Deadline;
use App\Services\ConferenceDeadlineService;

beforeEach(function () {
    $this->conference = createTestConference([
        'name' => 'Deadline Test Conf',
    ]);
    $this->service = app(ConferenceDeadlineService::class);
});

test('getOrCreateDeadlines initializes defaults if none exist', function () {
    expect(Deadline::where('conference_id', $this->conference->conference_id)->exists())->toBeFalse();

    $deadline = $this->service->getOrCreateDeadlines($this->conference->conference_id);

    expect($deadline->exists)->toBeTrue();
    expect($deadline->abstract_deadline)->not->toBeNull();
    expect($deadline->fullpaper_deadline)->not->toBeNull();
    expect(Deadline::where('conference_id', $this->conference->conference_id)->count())->toBe(1);
});

test('getOrCreateDeadlines returns existing deadline without duplicating', function () {
    $existing = Deadline::create([
        'conference_id' => $this->conference->conference_id,
        'abstract_deadline' => '2026-05-01 12:00:00',
        'fullpaper_deadline' => '2026-06-01 12:00:00',
        'cameraready_paper_deadline' => '2026-07-01 12:00:00',
        'participant_registration_deadline' => '2026-08-01 12:00:00',
        'review_deadline' => '2026-06-15 12:00:00',
        'listener_payment_deadline' => '2026-08-15 12:00:00',
        'presenter_payment_deadline' => '2026-07-15 12:00:00',
    ]);

    $fetched = $this->service->getOrCreateDeadlines($this->conference->conference_id);

    expect($fetched->deadline_id)->toBe($existing->deadline_id);
    expect(Deadline::where('conference_id', $this->conference->conference_id)->count())->toBe(1);
});

test('updateDeadlines updates all deadline fields accurately', function () {
    $this->service->getOrCreateDeadlines($this->conference->conference_id);

    $updated = $this->service->updateDeadlines($this->conference->conference_id, [
        'abstract_deadline' => '2026-10-01 23:59:59',
        'fullpaper_deadline' => '2026-11-01 23:59:59',
        'cameraready_paper_deadline' => '2026-12-01 23:59:59',
        'participant_registration_deadline' => '2026-12-15 23:59:59',
        'review_deadline' => '2026-11-15 23:59:59',
        'listener_payment_deadline' => '2026-12-20 23:59:59',
        'presenter_payment_deadline' => '2026-12-10 23:59:59',
    ]);

    expect($updated->abstract_deadline->format('Y-m-d H:i:s'))->toBe('2026-10-01 23:59:59');
    expect($updated->fullpaper_deadline->format('Y-m-d H:i:s'))->toBe('2026-11-01 23:59:59');
});
