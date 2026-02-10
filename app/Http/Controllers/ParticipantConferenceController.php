<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class ParticipantConferenceController extends Controller
{
    public function index()
    {
        $conferences = DB::table('conference')
            ->where('conference_status', 'active')
            ->orderBy('conference_date', 'asc')
            ->get();

        return view('participant.conference_list', compact('conferences'));
    }

    public function show($conference_id)
    {
        $conference = DB::table('conference')
            ->where('conference_id', $conference_id)
            ->where('conference_status', 'active')
            ->first();

        if (!$conference) {
            abort(404, 'Conference not found or not available.');
        }

        $participantId = session('user_id');

        // 🔹 Check if participant already registered
        $registration = DB::table('conference_registration')
            ->where('conference_id', $conference_id)
            ->where('participant_id', $participantId)
            ->first();

        // 🔹 Fetch registration fee categories for this conference
        $fees = DB::table('conference_fees')
            ->where('conference_id', $conference_id)
            ->orderBy('fee_id')
            ->get();

        // 🔹 Fetch tracks
        $tracks = DB::table('track')
            ->where('conference_id', $conference_id)
            ->get();

        // 🔹 Fetch deadlines
        $deadlines = DB::table('deadline')
            ->where('conference_id', $conference_id)
            ->first();

        return view('participant.conference_detail', compact('conference', 'registration', 'fees', 'tracks', 'deadlines'));
    }


}
