<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommitteeParticipantController extends Controller
{
    /**
     * List all registered participants for a specific conference.
     */
    public function index($conference_id)
    {
        $conference = DB::table('conference')
            ->where('conference_id', $conference_id)
            ->first();

        if (!$conference) {
            abort(404);
        }

        // Fetch participants joined with their registration details for this conference
        $participants = DB::table('participant')
            ->join('conference_registration', 'participant.participant_id', '=', 'conference_registration.participant_id')
            ->where('conference_registration.conference_id', $conference_id)
            ->select(
                'participant.*',
                'conference_registration.registration_id',
                'conference_registration.registration_type',
                'conference_registration.registration_status',
                'conference_registration.registration_date as registered_at'
            )
            ->orderBy('conference_registration.registration_date', 'desc')
            ->get();

        return view('committee.participants.index', compact('conference', 'participants'));
    }

    /**
     * Show detailed information about a registered participant.
     */
    public function show($conference_id, $participant_id)
    {
        $conference = DB::table('conference')
            ->where('conference_id', $conference_id)
            ->first();

        if (!$conference) {
            abort(404);
        }

        $participant = DB::table('participant')
            ->join('conference_registration', 'participant.participant_id', '=', 'conference_registration.participant_id')
            ->where('conference_registration.conference_id', $conference_id)
            ->where('participant.participant_id', $participant_id)
            ->select(
                'participant.*',
                'conference_registration.registration_id',
                'conference_registration.registration_type',
                'conference_registration.registration_status',
                'conference_registration.registration_date as registered_at'
            )
            ->first();

        if (!$participant) {
            abort(404);
        }

        return view('committee.participants.show', compact('conference', 'participant'));
    }
}
