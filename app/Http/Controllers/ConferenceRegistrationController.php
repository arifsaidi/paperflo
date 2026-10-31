<?php

namespace App\Http\Controllers;

use App\Services\ConferenceRegistrationService;
use Illuminate\Http\Request;
use InvalidArgumentException;

class ConferenceRegistrationController extends Controller
{
    public function __construct(
        protected ConferenceRegistrationService $registrationService
    ) {}

    public function store(Request $request, $conference_id)
    {
        $request->validate([
            'registration_type' => 'required|in:listener,author',
            'registration_category' => 'required|string|max:100',
        ]);

        $participantId = session('user_id');

        try {
            $this->registrationService->register(
                $conference_id,
                $participantId,
                $request->registration_type,
                $request->registration_category
            );

            return back()->with('success', 'You have successfully registered for this conference.');
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function show($conference_id)
    {
        $participantId = session('user_id');

        $data = $this->registrationService->getRegistrationViewData($conference_id, $participantId);

        return view('participant.my_registration', $data);
    }

    public function confirmPresenter($conference_id)
    {
        $participantId = session('user_id');

        $this->registrationService->confirmPresenter($conference_id, $participantId);

        return redirect()
            ->route('participant.registration.show', $conference_id)
            ->with('success', 'Presenter registration confirmed.');
    }

    public function payListener(Request $request, $conference_id)
    {
        $participantId = session('user_id');

        try {
            $this->registrationService->payListener($conference_id, $participantId);

            return redirect()
                ->route('participant.registration.show', $conference_id)
                ->with('success', 'Registration fee payment recorded successfully.');
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
