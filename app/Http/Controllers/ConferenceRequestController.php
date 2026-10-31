<?php

namespace App\Http\Controllers;

use App\Services\ConferenceRequestService;
use Illuminate\Http\Request;
use InvalidArgumentException;

class ConferenceRequestController extends Controller
{
    public function __construct(
        protected ConferenceRequestService $requestService
    ) {}

    // Participant form
    public function create()
    {
        return view('conference_request.create');
    }

    // Store request
    public function store(Request $request)
    {
        $validated = $request->validate([
            'conference_title' => 'required|string|max:255',
            'org_name' => 'required|string|max:255',
            'org_type' => 'required|string|max:100',
            'org_address' => 'required|string',
            'applicant_name' => 'required|string|max:255',
            'applicant_email' => 'required|email',
            'applicant_phone' => 'required|string|max:30',
        ]);

        $participantId = session('user_type') === 'participant' ? session('user_id') : null;
        $superadminId = session('user_type') === 'super_admin' ? session('user_id') : 1;

        $this->requestService->submitRequest($validated, $participantId, $superadminId);

        return redirect()->back()->with('success', 'Conference request submitted.');
    }

    // Super Admin: list requests
    public function index()
    {
        $requests = $this->requestService->getAllRequests();

        return view('conference_request.index', compact('requests'));
    }

    // Super Admin: view request detail
    public function show($id)
    {
        $request = $this->requestService->getRequest($id);

        if (!$request) {
            abort(404);
        }

        return view('conference_request.show', compact('request'));
    }

    // Approve request
    public function approve($id)
    {
        try {
            $this->requestService->approveRequest($id);

            return redirect()->back()->with('success', 'Conference approved, created, and email sent to organizer.');
        } catch (InvalidArgumentException $e) {
            abort(400, $e->getMessage());
        }
    }

    // Reject request
    public function reject($id)
    {
        try {
            $this->requestService->rejectRequest($id);

            return redirect()->back()->with('success', 'Conference rejected.');
        } catch (InvalidArgumentException $e) {
            abort(400, $e->getMessage());
        }
    }
}
