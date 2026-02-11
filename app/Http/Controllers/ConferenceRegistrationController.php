<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConferenceRegistrationController extends Controller
{
    public function store(Request $request, $conference_id)
    {
        // 1. Check conference is active
        $conference = DB::table('conference')
            ->where('conference_id', $conference_id)
            ->where('conference_status', 'active')
            ->first();

        if (!$conference) {
            abort(403, 'Conference is not open for registration.');
        }

        $participantId = session('user_id');

        // 2. Prevent duplicate registration
        $exists = DB::table('conference_registration')
            ->where('conference_id', $conference_id)
            ->where('participant_id', $participantId)
            ->exists();

        if ($exists) {
            return back()->with('error', 'You have already registered for this conference.');
        }

        // 3. Validate input
        $request->validate([
            'registration_type' => 'required|in:listener,author',
            'registration_category' => 'required|string|max:100',
        ]);


        // 4. Register
        DB::table('conference_registration')->insert([
            'conference_id' => $conference_id,
            'participant_id' => $participantId,
            'registration_type' => $request->registration_type,
            'registration_category' => $request->registration_category,
            'registration_date' => now(),
            'registration_status' => 'registered',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'You have successfully registered for this conference.');
    }

    public function show($conference_id)
    {
        $participantId = session('user_id');

        $registration = DB::table('conference_registration')
            ->where('conference_id', $conference_id)
            ->where('participant_id', $participantId)
            ->first();

        $conference = DB::table('conference')
            ->where('conference_id', $conference_id)
            ->first();

        // ✅ Presenter invitation/assignment check (for this conference)
        $participant = DB::table('participant')
            ->where('participant_id', $participantId)
            ->select('participant_id', 'email')
            ->first();

        $presenterAssignment = DB::table('presenter_assignment')
            ->join('paper', 'paper.paper_id', '=', 'presenter_assignment.paper_id')
            ->where('paper.conference_id', $conference_id)
            ->where(function ($q) use ($participantId, $participant) {

                // presenter selected using account
                $q->where('presenter_assignment.participant_id', $participantId);

                // presenter selected using email (no account)
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

        // ✅ Only block user if NOT registered AND NOT invited presenter
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
            $registrationFee = DB::table('conference_fees')
                ->where('conference_id', $conference_id)
                ->where('name', $registration->registration_category)
                ->first();
        }

        return view('participant.my_registration', compact(
            'registration',
            'conference',
            'isInvitedPresenter',
            'presenterRegistrationStatus',
            'listenerPayment',
            'registrationFee'
        ));
    }



    public function confirmPresenter($conference_id)
    {
        $participantId = session('user_id');

        $participant = DB::table('participant')
            ->where('participant_id', $participantId)
            ->select('participant_id', 'email')
            ->first();

        if (!$participant) {
            abort(403, 'Invalid participant session.');
        }

        // 1) Find presenter assignment for this conference
        //    - match by participant_id (if already linked)
        //    - OR match by presenter_email (when presenter was selected before account existed)
        $assignment = DB::table('presenter_assignment')
            ->join('paper', 'paper.paper_id', '=', 'presenter_assignment.paper_id')
            ->where('paper.conference_id', $conference_id)
            ->where(function ($q) use ($participantId, $participant) {
                $q->where('presenter_assignment.participant_id', $participantId);

                if (!empty($participant->email)) {
                    $q->orWhere('presenter_assignment.presenter_email', $participant->email);
                }
            })
            // allow both statuses just in case you used different wording earlier
            ->whereIn('presenter_assignment.presenter_status', ['Assigned', 'Pending Confirmation'])
            ->select('presenter_assignment.presenter_assignment_id')
            ->first();

        if (!$assignment) {
            abort(403, 'You are not invited as a presenter for this conference.');
        }

        // 2) Confirm + link the presenter account
        DB::table('presenter_assignment')
            ->where('presenter_assignment_id', $assignment->presenter_assignment_id)
            ->update([
                'participant_id' => $participantId,
                'presenter_status' => 'Confirmed',
                'updated_at' => now(),
            ]);

        // 3) Auto-create conference registration (IF NOT EXISTS)
        $exists = DB::table('conference_registration')
            ->where('conference_id', $conference_id)
            ->where('participant_id', $participantId)
            ->exists();

        if (!$exists) {
            DB::table('conference_registration')->insert([
                'conference_id' => $conference_id,
                'participant_id' => $participantId,
                'registration_type' => 'presenter',
                'registration_category' => 'presenter',
                'registration_status' => 'confirmed',
                'registration_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()
            ->route('participant.registration.show', $conference_id)
            ->with('success', 'Presenter registration confirmed.');
    }



    public function payListener(Request $request, $conference_id)
    {
        $participantId = session('user_id');

        $registration = DB::table('conference_registration')
            ->where('conference_id', $conference_id)
            ->where('participant_id', $participantId)
            ->where('registration_type', 'listener')
            ->first();

        if (!$registration) {
            abort(403, 'You are not registered as a listener for this conference.');
        }

        // Prevent double payment
        $existing = DB::table('listener_payment')
            ->where('registration_id', $registration->registration_id)
            ->first();

        if ($existing) {
            return back()->with('error', 'Payment has already been recorded.');
        }

        // Get fee amount
        $fee = DB::table('conference_fees')
            ->where('conference_id', $conference_id)
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

        return redirect()
            ->route('participant.registration.show', $conference_id)
            ->with('success', 'Registration fee payment recorded successfully.');
    }
}
