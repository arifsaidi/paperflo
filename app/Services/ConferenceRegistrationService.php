<?php

namespace App\Services;

use App\Models\Conference;
use App\Models\ConferenceFee;
use App\Models\ConferenceRegistration;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ConferenceRegistrationService
{
    /**
     * Register a participant for a conference.
     */
    public function register(
        int $conferenceId,
        int $participantId,
        string $registrationType,
        string $registrationCategory
    ): ConferenceRegistration {
        // 1. Check conference is active
        $conference = Conference::active()->where('conference_id', $conferenceId)->first();

        if (!$conference) {
            abort(403, 'Conference is not open for registration.');
        }

        // 2. Prevent duplicate registration
        $alreadyRegistered = ConferenceRegistration::where('conference_id', $conferenceId)
            ->where('participant_id', $participantId)
            ->exists();

        if ($alreadyRegistered) {
            throw new InvalidArgumentException('You have already registered for this conference.');
        }

        // 3. Create registration
        return ConferenceRegistration::create([
            'conference_id' => $conferenceId,
            'participant_id' => $participantId,
            'registration_type' => $registrationType,
            'registration_category' => $registrationCategory,
            'registration_status' => 'registered',
            'registration_date' => now(),
        ]);
    }

    /**
     * Retrieve participant's registration for a conference.
     */
    public function getRegistration(int $conferenceId, int $participantId): ?ConferenceRegistration
    {
        return ConferenceRegistration::where('conference_id', $conferenceId)
            ->where('participant_id', $participantId)
            ->first();
    }

    /**
     * Get view data for participant's registration page.
     */
    public function getRegistrationViewData(int $conferenceId, int $participantId): array
    {
        $conference = Conference::findOrFail($conferenceId);
        $registration = $this->getRegistration($conferenceId, $participantId);

        $participant = DB::table('participant')
            ->where('participant_id', $participantId)
            ->select('participant_id', 'email')
            ->first();

        $presenterAssignment = DB::table('presenter_assignment')
            ->join('paper', 'paper.paper_id', '=', 'presenter_assignment.paper_id')
            ->where('paper.conference_id', $conferenceId)
            ->where(function ($q) use ($participantId, $participant) {
                $q->where('presenter_assignment.participant_id', $participantId);
                if (!empty($participant?->email)) {
                    $q->orWhere('presenter_assignment.presenter_email', $participant->email);
                }
            })
            ->select(
                'presenter_assignment.presenter_status',
                'presenter_assignment.paper_id',
                'presenter_assignment.participant_id',
                'presenter_assignment.presenter_email'
            )
            ->first();

        $isInvitedPresenter = !empty($presenterAssignment);
        $presenterRegistrationStatus = $presenterAssignment->presenter_status ?? null;

        if (!$registration && !$isInvitedPresenter) {
            abort(403, 'You are not registered for this conference.');
        }

        // Listener payment check
        $listenerPayment = null;
        if ($registration && $registration->registration_type === 'listener') {
            $listenerPayment = DB::table('listener_payment')
                ->where('registration_id', $registration->registration_id)
                ->first();
        }

        // Get fee for this registration category
        $registrationFee = null;
        if ($registration) {
            $registrationFee = ConferenceFee::where('conference_id', $conferenceId)
                ->where('name', $registration->registration_category)
                ->first();
        }

        return [
            'registration' => $registration,
            'conference' => $conference,
            'isInvitedPresenter' => $isInvitedPresenter,
            'presenterRegistrationStatus' => $presenterRegistrationStatus,
            'listenerPayment' => $listenerPayment,
            'registrationFee' => $registrationFee,
        ];
    }

    /**
     * Confirm presenter attendance and auto-register if needed.
     */
    public function confirmPresenter(int $conferenceId, int $participantId): void
    {
        $participant = DB::table('participant')
            ->where('participant_id', $participantId)
            ->select('participant_id', 'email')
            ->first();

        if (!$participant) {
            abort(403, 'Invalid participant session.');
        }

        $assignment = DB::table('presenter_assignment')
            ->join('paper', 'paper.paper_id', '=', 'presenter_assignment.paper_id')
            ->where('paper.conference_id', $conferenceId)
            ->where(function ($q) use ($participantId, $participant) {
                $q->where('presenter_assignment.participant_id', $participantId);
                if (!empty($participant->email)) {
                    $q->orWhere('presenter_assignment.presenter_email', $participant->email);
                }
            })
            ->whereIn('presenter_assignment.presenter_status', ['Assigned', 'Pending Confirmation'])
            ->select('presenter_assignment.presenter_assignment_id')
            ->first();

        if (!$assignment) {
            abort(403, 'You are not invited as a presenter for this conference.');
        }

        DB::table('presenter_assignment')
            ->where('presenter_assignment_id', $assignment->presenter_assignment_id)
            ->update([
                'participant_id' => $participantId,
                'presenter_status' => 'Confirmed',
                'updated_at' => now(),
            ]);

        // Auto-create conference registration if not exists
        $exists = ConferenceRegistration::where('conference_id', $conferenceId)
            ->where('participant_id', $participantId)
            ->exists();

        if (!$exists) {
            ConferenceRegistration::create([
                'conference_id' => $conferenceId,
                'participant_id' => $participantId,
                'registration_type' => 'presenter',
                'registration_category' => 'presenter',
                'registration_status' => 'confirmed',
                'registration_date' => now(),
            ]);
        }
    }

    /**
     * Process listener registration fee payment.
     */
    public function payListener(int $conferenceId, int $participantId): void
    {
        $registration = ConferenceRegistration::where('conference_id', $conferenceId)
            ->where('participant_id', $participantId)
            ->where('registration_type', 'listener')
            ->first();

        if (!$registration) {
            abort(403, 'You are not registered as a listener for this conference.');
        }

        $existing = DB::table('listener_payment')
            ->where('registration_id', $registration->registration_id)
            ->first();

        if ($existing) {
            throw new InvalidArgumentException('Payment has already been recorded.');
        }

        $fee = ConferenceFee::where('conference_id', $conferenceId)
            ->where('name', $registration->registration_category)
            ->first();

        $amount = $fee ? $fee->amount : 0;

        DB::table('listener_payment')->insert([
            'name' => $registration->registration_category,
            'amount' => $amount,
            'payment_status' => 'Paid',
            'registration_id' => $registration->registration_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
