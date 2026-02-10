<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewAssignmentController extends Controller
{
    // List full papers in a conference + assign reviewers
    public function index($conference_id)
    {
        // 1) full papers submitted for this conference
        $fullpapers = DB::table('paper')
            ->join('conference', 'conference.conference_id', '=', 'paper.conference_id')
            ->leftJoin('track', 'track.track_id', '=', 'paper.track_id')
            ->join('fullpaper_submission', 'fullpaper_submission.paper_id', '=', 'paper.paper_id')
            ->where('paper.conference_id', $conference_id)
            ->select([
                'paper.paper_id',
                'paper.title',
                DB::raw('COALESCE(track.name, "-") as track_name'),
                'conference.name as conference_name',
                'fullpaper_submission.fullpaper_id',
                'fullpaper_submission.fullpaper_status',
                'fullpaper_submission.submitted_date as fullpaper_submitted_date',
            ])
            ->orderByDesc('fullpaper_submission.fullpaper_id')
            ->get();

        // 2) reviewers invited/assigned to this conference
        // We assume invite_status='Accepted' means active reviewer for conference
        $reviewers = DB::table('conference_reviewer_assignment')
            ->join('reviewer', 'reviewer.reviewer_id', '=', 'conference_reviewer_assignment.reviewer_id')
            ->where('conference_reviewer_assignment.conference_id', $conference_id)
            ->where('conference_reviewer_assignment.invite_status', 'Accepted')
            ->select([
                'reviewer.reviewer_id',
                'reviewer.name',
                'reviewer.email',
                'reviewer.area',
                'conference_reviewer_assignment.reviewer_type',
            ])
            ->orderBy('reviewer.name')
            ->get();

        // 3) existing assignments for display
        $assignments = DB::table('review_assignment')
            ->join('reviewer', 'reviewer.reviewer_id', '=', 'review_assignment.reviewer_id')
            ->join('fullpaper_submission', 'fullpaper_submission.fullpaper_id', '=', 'review_assignment.fullpaper_id')
            ->select([
                'review_assignment.review_id',
                'review_assignment.fullpaper_id',
                'review_assignment.reviewer_id',
                'review_assignment.review_status',
                'review_assignment.score',
                'reviewer.name as reviewer_name',
                'reviewer.email as reviewer_email',
            ])
            ->get()
            ->groupBy('fullpaper_id');

        return view('committee.review_assignments.index', compact(
            'conference_id',
            'fullpapers',
            'reviewers',
            'assignments'
        ));
    }

    // Assign reviewer to a full paper
    public function assign(Request $request, $conference_id, $fullpaper_id)
    {
        $request->validate([
            'reviewer_id' => 'required|integer',
        ]);

        // Ensure fullpaper belongs to this conference
        $fullpaper = DB::table('fullpaper_submission')
            ->join('paper', 'paper.paper_id', '=', 'fullpaper_submission.paper_id')
            ->where('fullpaper_submission.fullpaper_id', $fullpaper_id)
            ->where('paper.conference_id', $conference_id)
            ->select('fullpaper_submission.fullpaper_id')
            ->first();

        if (!$fullpaper) {
            abort(404);
        }

        // Ensure reviewer is accepted in this conference
        $validReviewer = DB::table('conference_reviewer_assignment')
            ->where('conference_id', $conference_id)
            ->where('reviewer_id', $request->reviewer_id)
            ->where('invite_status', 'Accepted')
            ->exists();

        if (!$validReviewer) {
            return back()->with('error', 'Reviewer is not active for this conference.');
        }

        // Prevent duplicate assignment (one row per reviewer per fullpaper)
        $exists = DB::table('review_assignment')
            ->where('fullpaper_id', $fullpaper_id)
            ->where('reviewer_id', $request->reviewer_id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'This reviewer is already assigned to this paper.');
        }

        DB::table('review_assignment')->insert([
            'score' => 0, // placeholder until submitted
            'comment' => null,
            'review_status' => 'Assigned',
            'fullpaper_id' => $fullpaper_id,
            'reviewer_id' => $request->reviewer_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Reviewer assigned successfully.');
    }
}
