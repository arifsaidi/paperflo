<?php

namespace App\Http\Controllers;

use App\Services\ConferenceDeadlineService;
use App\Services\ConferenceService;
use Illuminate\Http\Request;

class ConferenceDeadlineController extends Controller
{
    public function __construct(
        protected ConferenceService $conferenceService,
        protected ConferenceDeadlineService $deadlineService
    ) {}

    public function index($conference_id)
    {
        $conference = $this->conferenceService->findConferenceOrFail($conference_id);
        $deadline = $this->deadlineService->getOrCreateDeadlines($conference_id);

        return view('conference.deadlines.index', compact('conference', 'deadline'));
    }

    public function update(Request $request, $conference_id)
    {
        $validated = $request->validate([
            'abstract_deadline' => 'required|date',
            'fullpaper_deadline' => 'required|date',
            'cameraready_paper_deadline' => 'required|date',
            'participant_registration_deadline' => 'required|date',
            'review_deadline' => 'required|date',
            'listener_payment_deadline' => 'required|date',
            'presenter_payment_deadline' => 'required|date',
        ]);

        $this->deadlineService->updateDeadlines($conference_id, $validated);

        return back()->with('success', 'Deadlines updated successfully.');
    }
}
