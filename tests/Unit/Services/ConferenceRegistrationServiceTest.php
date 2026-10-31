<?php

use App\Models\ConferenceFee;
use App\Services\ConferenceRegistrationService;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->conference = createTestConference([
        'name' => 'Reg Test Conf',
        'conference_status' => 'active',
    ]);

    $this->participantId = createTestParticipant();

    $this->service = app(ConferenceRegistrationService::class);
});

test('register creates registration for active conference', function () {
    $reg = $this->service->register(
        $this->conference->conference_id,
        $this->participantId,
        'listener',
        'Standard Listener'
    );

    expect($reg->exists)->toBeTrue();
    expect($reg->registration_type)->toBe('listener');
    expect($reg->registration_status)->toBe('registered');
});

test('register prevents duplicate registrations for same user and conference', function () {
    $this->service->register(
        $this->conference->conference_id,
        $this->participantId,
        'listener',
        'Standard'
    );

    expect(fn () => $this->service->register(
        $this->conference->conference_id,
        $this->participantId,
        'author',
        'Presenter'
    ))->toThrow(InvalidArgumentException::class, 'already registered');
});

test('payListener marks listener registration as paid', function () {
    ConferenceFee::create([
        'conference_id' => $this->conference->conference_id,
        'name' => 'Standard Listener',
        'amount' => 120.00,
        'currency' => 'MYR',
    ]);

    $reg = $this->service->register(
        $this->conference->conference_id,
        $this->participantId,
        'listener',
        'Standard Listener'
    );

    $this->service->payListener($this->conference->conference_id, $this->participantId);

    $payment = DB::table('listener_payment')->where('registration_id', $reg->registration_id)->first();

    expect($payment)->not->toBeNull();
    expect($payment->payment_status)->toBe('Paid');
    expect((float) $payment->amount)->toBe(120.00);

    // Reject double payment
    expect(fn () => $this->service->payListener($this->conference->conference_id, $this->participantId))
        ->toThrow(InvalidArgumentException::class, 'Payment has already been recorded');
});
