<?php

namespace App\Http\Controllers;

use App\Services\CommitteeService;
use App\Services\ConferenceService;
use Illuminate\Http\Request;

class CommitteeAdminController extends Controller
{
    public function __construct(
        protected CommitteeService $committeeService,
        protected ConferenceService $conferenceService
    ) {}

    public function index($conference_id)
    {
        $conference = $this->conferenceService->findConferenceOrFail($conference_id);

        $admins = $this->committeeService->getConferenceAdmins($conference_id);

        $currentUserId = session('user_id');
        $currentUserEmail = session('user_email');

        $isOrganizer = $this->committeeService->isOrganizer($conference_id, $currentUserId, $currentUserEmail);
        $isManager = $this->committeeService->isManager($currentUserId);

        return view('committee.admins.index', compact('conference', 'admins', 'isOrganizer', 'isManager'));
    }

    public function show($conference_id, $admin_id)
    {
        $conference = $this->conferenceService->findConferenceOrFail($conference_id);

        $admin = $this->committeeService->getAdminDetail($conference_id, $admin_id);

        if (!$admin) {
            abort(404);
        }

        $currentUserId = session('user_id');
        $isManager = $this->committeeService->isManager($currentUserId);

        return view('committee.admins.show', compact('conference', 'admin', 'isManager'));
    }

    public function updateName(Request $request, $conference_id, $admin_id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $currentUserId = session('user_id');

        $this->committeeService->updateAdminName($admin_id, $request->name, $currentUserId);

        return redirect()->back()->with('success', 'Name updated successfully.');
    }

    // Organizer invites Manager
    public function inviteManager(Request $request, $conference_id)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $currentUserId = session('user_id');

        $this->committeeService->inviteManager($conference_id, $request->email, $currentUserId);

        return redirect()->back()->with('success', 'Conference Manager invited successfully.');
    }

    // Manager invites Admin
    public function store(Request $request, $conference_id)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $this->committeeService->inviteAdmin($conference_id, $request->email);

        return redirect()->back()->with('success', 'Conference Admin invited successfully.');
    }

    public function updateAccessLevel(Request $request, $conference_id, $admin_id)
    {
        $request->validate([
            'access_level' => 'required|in:admin,manager',
        ]);

        $currentUserId = session('user_id');

        $message = $this->committeeService->updateAccessLevel(
            $conference_id,
            $admin_id,
            $request->access_level,
            $currentUserId
        );

        return redirect()->back()->with('success', $message);
    }

    public function destroy($conference_id, $admin_id)
    {
        $this->committeeService->removeAdmin($conference_id, $admin_id);

        return redirect()->back()->with('success', 'Admin removed from conference.');
    }
}
