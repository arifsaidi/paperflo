<?php

namespace App\Http\Controllers;

use App\Services\ConferenceService;

class ParticipantConferenceController extends Controller
{
    public function __construct(
        protected ConferenceService $conferenceService
    ) {}

    public function index()
    {
        $conferences = $this->conferenceService->getActiveConferences();

        return view('participant.conference_list', compact('conferences'));
    }

    public function show($conference_id)
    {
        $participantId = session('user_id');

        $detail = $this->conferenceService->getConferenceDetail($conference_id, $participantId);

        return view('participant.conference_detail', [
            'conference' => $detail['conference'],
            'registration' => $detail['registration'],
            'fees' => $detail['fees'],
            'tracks' => $detail['tracks'],
            'deadlines' => $detail['deadlines'],
        ]);
    }
}
