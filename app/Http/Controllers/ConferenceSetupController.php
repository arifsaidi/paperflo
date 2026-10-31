<?php

namespace App\Http\Controllers;

use App\Services\ConferenceService;
use Illuminate\Http\Request;
use InvalidArgumentException;

class ConferenceSetupController extends Controller
{
    public function __construct(
        protected ConferenceService $conferenceService
    ) {}

    public function edit($conference_id)
    {
        $conference = $this->conferenceService->findConferenceOrFail($conference_id);

        return view('conference.setup', compact('conference'));
    }

    public function update(Request $request, $conference_id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'chairman_name' => 'required|string|max:255',
            'chairman_email' => 'required|email|max:255',
            'conference_phone' => 'nullable|string|max:20',
            'conference_email' => 'nullable|email|max:255',
            'conference_website' => 'nullable|url|max:255',
            'publication' => 'nullable|string|max:255',
            'conference_venue' => 'required|string|max:255',
            'secretariat_address' => 'nullable|string',
            'conference_date' => 'nullable|date',
        ]);

        try {
            $this->conferenceService->updateConferenceInfo(
                $conference_id,
                $request->all(),
                $request->file('logo')
            );

            return back()->with('success', 'Conference information updated.');
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function editContents($conference_id)
    {
        $conference = $this->conferenceService->findConferenceOrFail($conference_id);

        return view('conference.contents', compact('conference'));
    }

    public function updateContents(Request $request, $conference_id)
    {
        $request->validate([
            'contents' => 'nullable|string',
        ]);

        try {
            $this->conferenceService->updateContents($conference_id, $request->contents);

            return back()->with('success', 'Conference description updated.');
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function editStatus($conference_id)
    {
        $conference = $this->conferenceService->findConferenceOrFail($conference_id);

        return view('conference.status', compact('conference'));
    }

    public function activate($conference_id)
    {
        try {
            $this->conferenceService->activateConference($conference_id);

            return back()->with('success', 'Conference activated successfully.');
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
