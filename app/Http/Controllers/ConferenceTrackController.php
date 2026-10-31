<?php

namespace App\Http\Controllers;

use App\Services\ConferenceService;
use App\Services\ConferenceTrackService;
use Illuminate\Http\Request;

class ConferenceTrackController extends Controller
{
    public function __construct(
        protected ConferenceService $conferenceService,
        protected ConferenceTrackService $trackService
    ) {}

    public function index($conference_id)
    {
        $conference = $this->conferenceService->findConferenceOrFail($conference_id);
        $tracks = $this->trackService->getTracks($conference_id);

        return view('conference.tracks.index', compact('conference', 'tracks'));
    }

    public function store(Request $request, $conference_id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $this->trackService->addTrack($conference_id, $request->name);

        return back()->with('success', 'Track added successfully.');
    }

    public function destroy($conference_id, $track_id)
    {
        $result = $this->trackService->deleteTrack($conference_id, $track_id);

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return back()->with('success', $result['message']);
    }
}
