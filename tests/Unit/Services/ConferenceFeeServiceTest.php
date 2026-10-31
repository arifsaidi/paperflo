<?php

use App\Models\ConferenceFee;
use App\Services\ConferenceFeeService;

beforeEach(function () {
    $this->conference = createTestConference([
        'name' => 'Fee Test Conf',
    ]);
    $this->service = app(ConferenceFeeService::class);
});

test('getFees returns all fee tiers for a conference', function () {
    ConferenceFee::create([
        'conference_id' => $this->conference->conference_id,
        'name' => 'Tier 1',
        'amount' => 100,
        'currency' => 'MYR',
    ]);
    ConferenceFee::create([
        'conference_id' => $this->conference->conference_id,
        'name' => 'Tier 2',
        'amount' => 200,
        'currency' => 'MYR',
    ]);

    $fees = $this->service->getFees($this->conference->conference_id);

    expect($fees)->toHaveCount(2);
});

test('syncFees updates existing fees and adds new tier', function () {
    $fee1 = ConferenceFee::create([
        'conference_id' => $this->conference->conference_id,
        'name' => 'Early Bird',
        'amount' => 150,
        'currency' => 'MYR',
    ]);

    $this->service->syncFees(
        $this->conference->conference_id,
        [
            $fee1->fee_id => ['name' => 'Early Bird Updated', 'amount' => 180],
        ],
        'Student Regular',
        90.00,
        'MYR'
    );

    $fee1->refresh();
    expect($fee1->name)->toBe('Early Bird Updated');
    expect((float) $fee1->amount)->toBe(180.0);

    $newFee = ConferenceFee::where('conference_id', $this->conference->conference_id)
        ->where('name', 'Student Regular')
        ->first();

    expect($newFee)->not->toBeNull();
    expect((float) $newFee->amount)->toBe(90.0);
});

test('deleteFee deletes target fee category', function () {
    $fee = ConferenceFee::create([
        'conference_id' => $this->conference->conference_id,
        'name' => 'To Delete',
        'amount' => 50,
        'currency' => 'MYR',
    ]);

    $deleted = $this->service->deleteFee($this->conference->conference_id, $fee->fee_id);

    expect($deleted)->toBeTrue();
    expect(ConferenceFee::find($fee->fee_id))->toBeNull();
});
