<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class CommitteeReviewController extends Controller
{
    public function paperReviews($conference_id, $paper_id)
    {
        // paper must belong to this conference
        $paper = DB::table('paper')
            ->join('conference', 'conference.conference_id', '=', 'paper.conference_id')
            ->leftJoin('track', 'track.track_id', '=', 'paper.track_id')
            ->where('paper.paper_id', $paper_id)
            ->where('paper.conference_id', $conference_id)
            ->select([
                'paper.paper_id',
                'paper.title',
                'paper.keywords',
                'paper.track_id',
                DB::raw('COALESCE(track.name, "-") as track_name'),
                'conference.name as conference_name',
            ])
            ->first();

        if (!$paper)
            abort(404);

        // authors
        $authors = DB::table('paper_author')
            ->where('paper_id', $paper_id)
            ->orderByDesc('is_corresponding_author')
            ->get();

        // fullpaper id (because review_assignment links to fullpaper_id)
        $fullpaper = DB::table('fullpaper_submission')
            ->where('paper_id', $paper_id)
            ->select('fullpaper_id')
            ->first();

        $fullpaperId = $fullpaper?->fullpaper_id;

        // reviewer list for assigning (conference accepted reviewers)
        $reviewers = DB::table('conference_reviewer_assignment')
            ->join('reviewer', 'reviewer.reviewer_id', '=', 'conference_reviewer_assignment.reviewer_id')
            ->where('conference_reviewer_assignment.conference_id', $conference_id)
            ->where('conference_reviewer_assignment.invite_status', 'Accepted')
            ->select([
                'reviewer.reviewer_id',
                'reviewer.name',
                'reviewer.area',
                'reviewer.email',
            ])
            ->orderBy('reviewer.name')
            ->get();

        // existing assignments/reviews for this paper
        $assignments = collect();

        if ($fullpaperId) {
            $assignments = DB::table('review_assignment')
                ->join('reviewer', 'reviewer.reviewer_id', '=', 'review_assignment.reviewer_id')
                ->where('review_assignment.fullpaper_id', $fullpaperId)
                ->select([
                    'review_assignment.review_id',
                    'review_assignment.review_status',
                    'review_assignment.score',
                    'review_assignment.created_at as assigned_date', // use created_at as assigned date
                    'reviewer.reviewer_id',
                    'reviewer.name as reviewer_name',
                    'reviewer.area as reviewer_area',
                ])
                ->orderByDesc('review_assignment.review_id')
                ->get();
        }

        // paper mark (simple average of submitted reviews)
        $avgScore = null;
        $submitted = $assignments->where('review_status', 'Submitted');
        if ($submitted->count() > 0) {
            $avgScore = round($submitted->avg('score'), 2);
        }

        return view('committee.reviews.paper', compact(
            'conference_id',
            'paper',
            'authors',
            'fullpaperId',
            'reviewers',
            'assignments',
            'avgScore'
        ));
    }


    public function reviewDetails($conference_id, $paper_id, $review_id)
    {
        // Ensure paper belongs to conference
        $paper = DB::table('paper')
            ->leftJoin('track', 'track.track_id', '=', 'paper.track_id')
            ->where('paper.paper_id', $paper_id)
            ->where('paper.conference_id', $conference_id)
            ->select([
                'paper.paper_id',
                'paper.title',
                'paper.keywords',
                DB::raw('COALESCE(track.name, "-") as track_name'),
            ])
            ->first();

        if (!$paper)
            abort(404);

        // Get fullpaper_id for this paper (review_assignment links to fullpaper_id)
        $fullpaper = DB::table('fullpaper_submission')
            ->where('paper_id', $paper_id)
            ->select('fullpaper_id')
            ->first();

        if (!$fullpaper) {
            abort(404, 'Full paper not found for this paper.');
        }

        // Review record + reviewer info, ensure the review belongs to this paper's fullpaper
        $review = DB::table('review_assignment')
            ->join('reviewer', 'reviewer.reviewer_id', '=', 'review_assignment.reviewer_id')
            ->join('fullpaper_submission', 'fullpaper_submission.fullpaper_id', '=', 'review_assignment.fullpaper_id')
            ->join('paper', 'paper.paper_id', '=', 'fullpaper_submission.paper_id')
            ->join('conference', 'conference.conference_id', '=', 'paper.conference_id')
            ->where('review_assignment.review_id', $review_id)
            ->where('paper.paper_id', $paper_id)
            ->where('paper.conference_id', $conference_id)
            ->select(
                'review_assignment.*',
                'paper.paper_id',
                'paper.title',
                'paper.keywords',
                'conference.name as conference_name',
                'fullpaper_submission.file_path as fullpaper_file_path'
            )
            ->first();


        if (!$review)
            abort(404);

        $readonly = true;

        return view('committee.reviews.show', compact(
            'review',
            'paper',
            'conference_id',
            'readonly'
        ));

    }

    public function assignReviewer($conference_id, $paper_id, $reviewer_id)
    {
        // ensure paper belongs to conference
        $paper = DB::table('paper')
            ->where('paper_id', $paper_id)
            ->where('conference_id', $conference_id)
            ->first();

        if (!$paper) {
            return back()->with('error', 'Invalid paper for this conference.');
        }

        // get fullpaper_id
        $fullpaper = DB::table('fullpaper_submission')
            ->where('paper_id', $paper_id)
            ->first();

        if (!$fullpaper) {
            return back()->with('error', 'Full paper not found.');
        }

        // ensure reviewer is invited & accepted for this conference
        $invited = DB::table('conference_reviewer_assignment')
            ->where('conference_id', $conference_id)
            ->where('reviewer_id', $reviewer_id)
            ->where('invite_status', 'Accepted')
            ->exists();

        if (!$invited) {
            return back()->with('error', 'Reviewer is not invited to this conference.');
        }

        // prevent duplicate assignment
        $exists = DB::table('review_assignment')
            ->where('fullpaper_id', $fullpaper->fullpaper_id)
            ->where('reviewer_id', $reviewer_id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Reviewer already assigned to this paper.');
        }

        // assign reviewer
        DB::table('review_assignment')->insert([
            'fullpaper_id' => $fullpaper->fullpaper_id,
            'reviewer_id' => $reviewer_id,
            'review_status' => 'Assigned',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Reviewer assigned successfully.');
    }



}
