<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function show()
    {
        return view('auth.login');
    }

    public function showCommitteeLogin()
    {
        return view('auth.committee_login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $email = $request->email;
        $password = $request->password;

        // Try each user table (based on your ERD)
        if ($id = $this->attempt('super_admin', 'superadmin_id', $email, $password)) {
            return $this->finishLogin('super_admin', $id);
        }

        if ($id = $this->attempt('conference_admin', 'admin_id', $email, $password)) {
            return $this->finishLogin('conference_admin', $id);
        }

        if ($id = $this->attempt('reviewer', 'reviewer_id', $email, $password)) {
            return $this->finishLogin('reviewer', $id);
        }

        if ($id = $this->attempt('participant', 'participant_id', $email, $password)) {
            return $this->finishLogin('participant', $id);
        }

        return back()->with('error', 'Invalid email or password.');
    }

    public function logout()
    {
        session()->flush();
        return redirect()->route('login.form')->with('success', 'Logged out.');
    }

    private function attempt(string $table, string $pk, string $email, string $password): ?int
    {
        $row = DB::table($table)->where('email', $email)->first();
        if (!$row)
            return null;

        if (!Hash::check($password, $row->password))
            return null;

        return (int) $row->{$pk};
    }

    private function finishLogin(string $userType, int $userId)
    {
        session([
            'user_type' => $userType,
            'user_id' => $userId,
        ]);

        if ($userType === 'super_admin') {
            return redirect()->route('conference.request.index')->with('success', "Logged in as Super Admin.");
        }

        if ($userType === 'conference_admin') {
            $assignment = DB::table('conference_admin_assignment')
                ->where('admin_id', $userId)
                ->first();

            if ($assignment) {
                return redirect()->route('committee.dashboard', $assignment->conference_id)
                    ->with('success', "Logged in as Conference Admin.");
            }
            return redirect()->route('home')->with('success', "Logged in as Conference Admin.");
        }

        if ($userType === 'reviewer') {
            return redirect()->route('reviewer.reviews.index')->with('success', "Logged in as Reviewer.");
        }

        return redirect()->route('home')->with('success', "Logged in as {$userType}.");
    }
}
