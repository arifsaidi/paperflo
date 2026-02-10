<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ParticipantAuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register_participant');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:participant,email',
            'password' => 'required|string|min:6|max:50|confirmed',
            'institution' => 'nullable|string|max:255',
            'faculty' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
        ]);

        $participantId = DB::table('participant')->insertGetId([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // ✅ bcrypt
            'institution' => $request->institution,
            'faculty' => $request->faculty,
            'department' => $request->department,
            'registration_date' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 🔗 Link presenter assignment if this email was selected as presenter earlier
        DB::table('presenter_assignment')
            ->where('presenter_email', $request->email)
            ->whereNull('participant_id')
            ->update([
                'participant_id' => $participantId,
                'updated_at' => now(),
            ]);


        // (Optional) auto-login after register
        session([
            'user_id' => $participantId,
            'user_type' => 'participant',
        ]);

        return redirect()->route('participant.conferences')
            ->with('success', 'Account created successfully.');
    }
}
