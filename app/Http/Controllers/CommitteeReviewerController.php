<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class CommitteeReviewerController extends Controller
{
    public function allReviewers($conference_id)
    {
        $reviewers = DB::table('reviewer')
            ->select('reviewer_id', 'name', 'email', 'area', 'title', 'institution', 'created_at')
            ->orderBy('name')
            ->get();

        return view(
            'committee.reviewers.all',
            compact('reviewers', 'conference_id')
        );
    }



    public function show($conference_id, $reviewer_id)
    {
        $reviewer = DB::table('reviewer')
            ->where('reviewer_id', $reviewer_id)
            ->first();

        if (!$reviewer) {
            abort(404);
        }

        // check if already appointed
        $alreadyInvited = DB::table('conference_reviewer_assignment')
            ->where('conference_id', $conference_id)
            ->where('reviewer_id', $reviewer_id)
            ->exists();

        return view('committee.reviewers.show', compact(
            'reviewer',
            'conference_id',
            'alreadyInvited'
        ));
    }


    public function appoint($conference_id, $reviewer_id)
    {
        // 1. Check reviewer exists
        $reviewer = DB::table('reviewer')
            ->where('reviewer_id', $reviewer_id)
            ->first();

        if (!$reviewer) {
            abort(404);
        }

        // 2. Prevent duplicate invitation
        $exists = DB::table('conference_reviewer_assignment')
            ->where('conference_id', $conference_id)
            ->where('reviewer_id', $reviewer_id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Reviewer already appointed for this conference.');
        }

        // 3. Insert appointment
        DB::table('conference_reviewer_assignment')->insert([
            'conference_id' => $conference_id,
            'reviewer_id' => $reviewer_id,
            'invite_date' => now(),
            'invite_status' => 'Accepted',
            'reviewer_type' => request('reviewer_type', 'Regular'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Reviewer appointed successfully.');
    }


    // Invite Reviewer (create account)
    public function create($conference_id)
    {
        return view('committee.reviewers.create', compact('conference_id'));
    }

    public function store(Request $request, $conference_id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'nullable|string|max:50',
            'email' => 'required|email|max:255|unique:reviewer,email',
            'area' => 'nullable|string|max:255',
            'institution' => 'nullable|string|max:255',
            'temp_password' => 'required|string|min:6|max:50',
            'reviewer_type' => 'required|in:Regular,Primary,Secondary',
        ]);

        $reviewerId = null;

        DB::transaction(function () use ($request, $conference_id, &$reviewerId) {
            // 1) Create reviewer account
            $reviewerId = DB::table('reviewer')->insertGetId([
                'name' => $request->name,
                'title' => $request->title,
                'email' => $request->email,
                'password' => Hash::make($request->temp_password), // ✅ bcrypt
                'area' => $request->area,
                'institution' => $request->institution,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 2) Auto-appoint reviewer to this conference (no accept/reject)
            DB::table('conference_reviewer_assignment')->insert([
                'invite_date' => now(),
                'invite_status' => 'Accepted', // ✅ appointed immediately
                'reviewer_type' => $request->reviewer_type,
                'reviewer_id' => $reviewerId,
                'conference_id' => $conference_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 3) Assign REVIEWER role (role_id=4)
            DB::table('user_role')->insert([
                'user_type' => 'reviewer',
                'user_id' => $reviewerId,
                'role_id' => 4, // REVIEWER role
                'scope_type' => null,
                'scope_id' => null,
                'assigned_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        // 3) "Email" to localhost → we will log it (mail driver = log)
        // We'll send a simple raw email so you don't need mailable yet.
        $loginUrl = route('login.form');

        Mail::raw(
            "You have been invited as a Reviewer.\n\nLogin URL: {$loginUrl}\nEmail: {$request->email}\nTemporary Password: {$request->temp_password}\n\nPlease log in and change your password later (feature can be added).",
            function ($message) use ($request) {
                $message->to($request->email)
                    ->subject('PaperFlo Reviewer Invitation');
            }
        );

        return redirect()->route('committee.reviewers.all', $conference_id)
            ->with('success', 'Reviewer account created + appointed. Invitation email written to logs.');
    }

    public function remove($conference_id, $reviewer_id)
    {
        // 1) must exist as appointed in this conference
        $appointed = DB::table('conference_reviewer_assignment')
            ->where('conference_id', $conference_id)
            ->where('reviewer_id', $reviewer_id)
            ->exists();

        if (!$appointed) {
            return back()->with('error', 'This reviewer is not appointed for this conference.');
        }

        // 2) block removal if reviewer has ANY assigned reviews for papers in this conference
        // review_assignment -> fullpaper_submission -> paper (filter by conference)
        $hasAssignments = DB::table('review_assignment')
            ->join('fullpaper_submission', 'fullpaper_submission.fullpaper_id', '=', 'review_assignment.fullpaper_id')
            ->join('paper', 'paper.paper_id', '=', 'fullpaper_submission.paper_id')
            ->where('paper.conference_id', $conference_id)
            ->where('review_assignment.reviewer_id', $reviewer_id)
            ->exists();

        if ($hasAssignments) {
            return back()->with('error', 'Cannot remove: reviewer already has paper(s) assigned.');
        }

        // 3) remove from conference
        DB::table('conference_reviewer_assignment')
            ->where('conference_id', $conference_id)
            ->where('reviewer_id', $reviewer_id)
            ->delete();

        return back()->with('success', 'Reviewer removed from this conference.');
    }

}
