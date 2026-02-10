<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ParticipantProfileController extends Controller
{
    public function show()
    {
        $participantId = session('user_id');

        $participant = DB::table('participant')
            ->where('participant_id', $participantId)
            ->first();

        if (!$participant) {
            abort(404, 'Participant profile not found.');
        }

        return view('participant.profile.show', compact('participant'));
    }

    public function update(Request $request)
    {
        $participantId = session('user_id');

        $participant = DB::table('participant')
            ->where('participant_id', $participantId)
            ->first();

        if (!$participant) {
            abort(404, 'Participant profile not found.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'institution' => 'nullable|string|max:255',
            'faculty' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'phone_num' => 'nullable|string|max:30',
        ]);

        DB::table('participant')
            ->where('participant_id', $participantId)
            ->update([
                'name' => $request->name,
                'institution' => $request->institution,
                'faculty' => $request->faculty,
                'department' => $request->department,
                'address' => $request->address,
                'phone_num' => $request->phone_num,
                'updated_at' => now(),
            ]);

        return redirect()->route('participant.profile.show')
            ->with('success', 'Profile updated successfully.');
    }

    public function editPassword()
    {
        return view('participant.profile.password');
    }

    public function updatePassword(Request $request)
    {
        $participantId = session('user_id');

        $participant = DB::table('participant')
            ->where('participant_id', $participantId)
            ->first();

        if (!$participant) {
            abort(404);
        }

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        if (!Hash::check($request->current_password, $participant->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }

        DB::table('participant')
            ->where('participant_id', $participantId)
            ->update([
                'password' => Hash::make($request->new_password),
                'updated_at' => now(),
            ]);

        return redirect()->route('participant.profile.show')
            ->with('success', 'Password changed successfully.');
    }
}
