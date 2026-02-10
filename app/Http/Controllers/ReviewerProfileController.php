<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ReviewerProfileController extends Controller
{
    public function show()
    {
        $reviewerId = session('user_id');

        $reviewer = DB::table('reviewer')
            ->where('reviewer_id', $reviewerId)
            ->select('reviewer_id', 'name', 'title', 'email', 'area', 'institution')
            ->first();

        if (!$reviewer) {
            abort(404, 'Reviewer profile not found.');
        }

        return view('reviewer.profile.show', compact('reviewer'));
    }

    public function update(Request $request)
    {
        $reviewerId = session('user_id');

        $reviewer = DB::table('reviewer')
            ->where('reviewer_id', $reviewerId)
            ->first();

        if (!$reviewer) {
            abort(404, 'Reviewer profile not found.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'nullable|string|max:50',
            'area' => 'nullable|string|max:255',
            'institution' => 'nullable|string|max:255',
        ]);

        DB::table('reviewer')
            ->where('reviewer_id', $reviewerId)
            ->update([
                'name' => $request->name,
                'title' => $request->title,
                'area' => $request->area,
                'institution' => $request->institution,
                'updated_at' => now(),
            ]);

        return redirect()->route('reviewer.profile.show')
            ->with('success', 'Profile updated successfully.');
    }

    public function editPassword()
    {
        return view('reviewer.profile.password');
    }

    public function updatePassword(Request $request)
    {
        $reviewerId = session('user_id');

        $reviewer = DB::table('reviewer')
            ->where('reviewer_id', $reviewerId)
            ->first();

        if (!$reviewer) {
            abort(404);
        }

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        // Check current password
        if (!Hash::check($request->current_password, $reviewer->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }

        DB::table('reviewer')
            ->where('reviewer_id', $reviewerId)
            ->update([
                'password' => Hash::make($request->new_password),
                'updated_at' => now(),
            ]);

        return redirect()->route('reviewer.profile.show')
            ->with('success', 'Password changed successfully.');
    }


}
