<?php

use App\Models\Paper;
use App\Models\Track;
use App\Services\ConferenceTrackService;

beforeEach(function () {
    $this->conference = createTestConference([
        'name' => 'Track Test Conf',
    ]);
    $this->service = app(ConferenceTrackService::class);
});

test('getTracks returns all tracks for a conference', function () {
    $this->service->addTrack($this->conference->conference_id, 'Software Engineering');
    $this->service->addTrack($this->conference->conference_id, 'Networks');

    $tracks = $this->service->getTracks($this->conference->conference_id);

    expect($tracks)->toHaveCount(2);
    expect($tracks->pluck('name')->toArray())->toContain('Software Engineering', 'Networks');
});

test('addTrack creates a track record successfully', function () {
    $track = $this->service->addTrack($this->conference->conference_id, 'Cloud Computing');

    expect($track->exists)->toBeTrue();
    expect($track->name)->toBe('Cloud Computing');
    expect($track->conference_id)->toBe($this->conference->conference_id);
});

test('deleteTrack successfully deletes unused track', function () {
    $track = $this->service->addTrack($this->conference->conference_id, 'Unused Track');

    $result = $this->service->deleteTrack($this->conference->conference_id, $track->track_id);

    expect($result['success'])->toBeTrue();
    expect(Track::find($track->track_id))->toBeNull();
});

test('deleteTrack prevents deletion when track has associated papers', function () {
    $track = $this->service->addTrack($this->conference->conference_id, 'Active Track');

    Paper::create([
        'title' => 'Paper on Cloud',
        'conference_id' => $this->conference->conference_id,
        'track_id' => $track->track_id,
    ]);

    $result = $this->service->deleteTrack($this->conference->conference_id, $track->track_id);

    expect($result['success'])->toBeFalse();
    expect($result['message'])->toContain('currently being used by papers');
    expect(Track::find($track->track_id))->not->toBeNull();
});
