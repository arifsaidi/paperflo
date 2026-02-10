<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CommitteeProfileController extends Controller
{
    /**
     * Show the profile edit form.
     */
    public function show()
    {
        $userId = session('user_id');
        $userType = session('user_type');

        if ($userType !== 'conference_admin') {
            abort(403, 'Unauthorized action.');
        }

        $admin = DB::table('conference_admin')
            ->where('admin_id', $userId)
            ->first();

        if (!$admin) {
            abort(404, 'User not found.');
        }

        // We also need conference_id for the layout
        // Assuming session or route logic handles this, but for layout we usually need $conference
        // However, the layout uses $conference_id or route param.
        // We'll pass $admin data.

        // Wait, the layout expects $conference variable usually?
        // Let's check committee.blade.php layout requirements.
        // It uses $conference_id ?? request()->route('conference_id')

        // If we access this page without conference_id in route, the layout might break if it relies on route param.
        // But if we put this under /conference/{conference_id}/my-account, we are good.
        // Let's assume the route will include conference_id.

        $conference_id = request()->route('conference_id');
        $conference = null;
        if ($conference_id) {
            $conference = DB::table('conference')->where('conference_id', $conference_id)->first();
        }

        return view('committee.profile.show', compact('admin', 'conference'));
    }

    /**
     * Update the profile (name, email).
     */
    public function update(Request $request)
    {
        $userId = session('user_id');
        $userType = session('user_type');

        if ($userType !== 'conference_admin') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:conference_admin,email,' . $userId . ',admin_id',
        ]);

        DB::table('conference_admin')
            ->where('admin_id', $userId)
            ->update([
                'name' => $request->name,
                'email' => $request->email,
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Show the password edit form.
     */
    public function editPassword()
    {
        $userId = session('user_id');
        $userType = session('user_type');

        if ($userType !== 'conference_admin') {
            abort(403);
        }

        $conference_id = request()->route('conference_id');
        $conference = null;
        if ($conference_id) {
            $conference = DB::table('conference')->where('conference_id', $conference_id)->first();
        }

        return view('committee.profile.password', compact('conference'));
    }

    /**
     * Update the password.
     */
    public function updatePassword(Request $request)
    {
        $userId = session('user_id');
        $userType = session('user_type');

        if ($userType !== 'conference_admin') {
            abort(403);
        }

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $admin = DB::table('conference_admin')
            ->where('admin_id', $userId)
            ->first();

        if (!Hash::check($request->current_password, $admin->password)) {
            return back()->withErrors(['current_password' => 'Current password does not match.']);
        }

        DB::table('conference_admin')
            ->where('admin_id', $userId)
            ->update([
                'password' => Hash::make($request->new_password),
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Password updated successfully.');
    }
}
