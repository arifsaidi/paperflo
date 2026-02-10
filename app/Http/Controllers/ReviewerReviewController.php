<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewerReviewController extends Controller
{
    // Reviewer list page
    public function index()
    {
        // assumes your session stores reviewer_id in user_id when reviewer logs in
        $reviewerId = session('user_id');

        $reviews = DB::table('review_assignment')
            ->join('fullpaper_submission', 'fullpaper_submission.fullpaper_id', '=', 'review_assignment.fullpaper_id')
            ->join('paper', 'paper.paper_id', '=', 'fullpaper_submission.paper_id')
            ->join('conference', 'conference.conference_id', '=', 'paper.conference_id')
            ->leftJoin('track', 'track.track_id', '=', 'paper.track_id')
            ->where('review_assignment.reviewer_id', $reviewerId)
            ->select([
                'review_assignment.review_id',
                'review_assignment.review_status',
                'review_assignment.score',
                'paper.paper_id',
                'paper.title',
                'conference.name as conference_name',
                DB::raw('COALESCE(track.name, "-") as track_name'),
                'fullpaper_submission.file_path as fullpaper_file_path',
                'fullpaper_submission.submitted_date as fullpaper_submitted_date',
            ])
            ->orderByDesc('review_assignment.review_id')
            ->get();

        return view('reviewer.reviews.index', compact('reviews'));
    }

    // Review form
    public function show($review_id)
    {
        $reviewerId = session('user_id');

        $review = DB::table('review_assignment')
            ->join('fullpaper_submission', 'fullpaper_submission.fullpaper_id', '=', 'review_assignment.fullpaper_id')
            ->join('paper', 'paper.paper_id', '=', 'fullpaper_submission.paper_id')
            ->join('conference', 'conference.conference_id', '=', 'paper.conference_id')
            ->where('review_assignment.review_id', $review_id)
            ->where('review_assignment.reviewer_id', $reviewerId)
            ->select([
                'review_assignment.*',
                'paper.paper_id',
                'paper.title',
                'paper.keywords',
                'conference.name as conference_name',
                'fullpaper_submission.file_path as fullpaper_file_path',
            ])
            ->first();

        if (!$review)
            abort(403);

        return view('reviewer.reviews.show', compact('review'));
    }


    // Submit review
    public function submit(Request $request, $review_id)
    {
        $reviewerId = session('user_id');

        $review = DB::table('review_assignment')
            ->where('review_id', $review_id)
            ->where('reviewer_id', $reviewerId)
            ->first();

        if (!$review)
            abort(403);

        if ($review->review_status === 'Submitted') {
            return back()->with('error', 'Review already submitted.');
        }

        $request->validate([
            'rate_originality' => 'required|integer|min:0|max:5',
            'rate_literature_depth' => 'required|integer|min:0|max:5',
            'rate_methodology' => 'required|integer|min:0|max:5',
            'rate_novelty' => 'required|integer|min:0|max:5',
            'rate_clarity' => 'required|integer|min:0|max:5',
            'rate_contribution' => 'required|integer|min:0|max:5',
            'accept_presentation' => 'required|in:Yes,No',
            'overall_comments' => 'nullable|string',
            'comments_to_author' => 'nullable|string',
            'propose_scopus' => 'nullable|in:Yes,No',
            'propose_non_scopus' => 'nullable|in:Yes,No',
            'revised_file' => 'nullable|file|max:20480',
        ]);

        $filePath = null;
        if ($request->hasFile('revised_file')) {
            $filePath = $request->file('revised_file')->store('review_revisions', 'public');
        }

        // Compute simple average score
        $score = round((
            $request->rate_originality +
            $request->rate_literature_depth +
            $request->rate_methodology +
            $request->rate_novelty +
            $request->rate_clarity +
            $request->rate_contribution
        ) / 6, 2);

        DB::table('review_assignment')
            ->where('review_id', $review_id)
            ->update([
                'rate_originality' => $request->rate_originality,
                'rate_literature_depth' => $request->rate_literature_depth,
                'rate_methodology' => $request->rate_methodology,
                'rate_novelty' => $request->rate_novelty,
                'rate_clarity' => $request->rate_clarity,
                'rate_contribution' => $request->rate_contribution,
                'accept_presentation' => $request->accept_presentation,
                'overall_comments' => $request->overall_comments,
                'comments_to_author' => $request->comments_to_author,
                'propose_scopus' => $request->propose_scopus,
                'propose_non_scopus' => $request->propose_non_scopus,
                'revised_file_path' => $filePath,
                'score' => $score,
                'review_status' => 'Submitted',
                'submitted_at' => now(),
                'updated_at' => now(),
            ]);

        return redirect()->route('reviewer.reviews.index')
            ->with('success', 'Review submitted successfully.');
    }

}
