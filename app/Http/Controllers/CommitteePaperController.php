<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Events\NotificationSent;

class CommitteePaperController extends Controller
{
    // 1️⃣ Paper list (landing page)
    public function index($conference_id)
    {
        $conference = DB::table('conference')
            ->where('conference_id', $conference_id)
            ->first();

        if (!$conference)
            abort(404);

        $papers = DB::table('paper')
            ->join('abstract_submission', 'abstract_submission.paper_id', '=', 'paper.paper_id')
            ->leftJoin('track', 'track.track_id', '=', 'paper.track_id')
            ->leftJoin('paper_author', function ($join) {
                $join->on('paper_author.paper_id', '=', 'paper.paper_id')
                    ->where('paper_author.is_corresponding_author', 1);
            })
            ->where('paper.conference_id', $conference_id)
            ->whereIn('abstract_submission.abstract_status', ['Submitted', 'Accepted', 'Rejected'])
            ->select(
                'paper.paper_id',
                'paper.title',
                'abstract_submission.abstract_status',
                'paper_author.author_name as main_author',
                DB::raw('COALESCE(track.name, CONCAT("Track #", paper.track_id)) as track_name')
            )
            ->orderBy('paper.paper_id')
            ->get();

        foreach ($papers as $p) {
            if ($p->abstract_status === 'Rejected') {
                $p->final_status = 'Rejected (Abstract)';
            } elseif (!empty($p->fullpaper_status) && $p->fullpaper_status === 'Rejected') {
                $p->final_status = 'Rejected (Full Paper)';
            } elseif (!empty($p->cameraready_file_path)) {
                $p->final_status = 'Accepted (Camera-ready Submitted)';
            } elseif (!empty($p->fullpaper_status) && $p->fullpaper_status === 'Accepted') {
                $p->final_status = 'Accepted (Pending Camera-ready)';
            } elseif ($p->abstract_status === 'Accepted') {
                $p->final_status = 'Abstract Accepted';
            } elseif ($p->abstract_status === 'Submitted') {
                $p->final_status = 'Under Review';
            } elseif ($p->abstract_status === 'Draft') {
                $p->final_status = 'Draft';
            } else {
                $p->final_status = '-';
            }
        }


        return view(
            'committee.papers.index',
            compact('conference_id', 'papers')
        );
    }

    // 2️⃣ Paper detail (abstract only)
    public function show($conference_id, $paper_id)
    {
        $paper = DB::table('paper')
            ->join('conference', 'conference.conference_id', '=', 'paper.conference_id')
            ->join('abstract_submission', 'abstract_submission.paper_id', '=', 'paper.paper_id')
            ->leftJoin('track', 'track.track_id', '=', 'paper.track_id')

            ->leftJoin('fullpaper_submission', 'fullpaper_submission.paper_id', '=', 'paper.paper_id')
            ->leftJoin('cameraready_submission', 'cameraready_submission.paper_id', '=', 'paper.paper_id')
            ->leftJoin('presenter_assignment', 'presenter_assignment.paper_id', '=', 'paper.paper_id')

            ->where('paper.paper_id', $paper_id)
            ->where('paper.conference_id', $conference_id)
            ->select(
                'paper.paper_id',
                'paper.title',
                'paper.keywords',
                'paper.conference_id',
                'conference.name as conference_name',

                'abstract_submission.abstract_text',
                'abstract_submission.abstract_status',
                'abstract_submission.submitted_date as abstract_submitted_date',

                DB::raw('COALESCE(track.name, CONCAT("Track #", paper.track_id)) as track_name'),

                'fullpaper_submission.file_path as fullpaper_file_path',
                'fullpaper_submission.submitted_date as fullpaper_submitted_date',
                'fullpaper_submission.fullpaper_status as fullpaper_status',

                'cameraready_submission.file_path as cameraready_file_path',
                'cameraready_submission.submitted_date as cameraready_submitted_date',
                'cameraready_submission.cameraready_status',

                'presenter_assignment.presenter_status',

            )
            ->first();

        if (!$paper)
            abort(404);

        // Authors list
        $authors = DB::table('paper_author')
            ->where('paper_id', $paper_id)
            ->orderByDesc('is_corresponding_author')
            ->get();

        // Affiliation (best-effort): only works for corresponding author linked to participant_id
        $correspondingAuthor = DB::table('paper_author')
            ->where('paper_id', $paper_id)
            ->where('is_corresponding_author', 1)
            ->first();

        $affiliation = null;
        if ($correspondingAuthor && $correspondingAuthor->participant_id) {
            $affiliation = DB::table('participant')
                ->where('participant_id', $correspondingAuthor->participant_id)
                ->select('institution', 'faculty', 'department')
                ->first();
        }

        $avgScore = DB::table('review_assignment')
            ->join('fullpaper_submission', 'fullpaper_submission.fullpaper_id', '=', 'review_assignment.fullpaper_id')
            ->where('fullpaper_submission.paper_id', $paper_id)
            ->where('review_assignment.review_status', 'Submitted')
            ->avg('review_assignment.score');

        // Fetch submitted reviews with ratings and comments
        $reviews = DB::table('review_assignment')
            ->join('fullpaper_submission', 'fullpaper_submission.fullpaper_id', '=', 'review_assignment.fullpaper_id')
            ->join('reviewer', 'reviewer.reviewer_id', '=', 'review_assignment.reviewer_id')
            ->where('fullpaper_submission.paper_id', $paper_id)
            ->where('review_assignment.review_status', 'Submitted')
            ->select(
                'review_assignment.review_id',
                'reviewer.name as reviewer_name',
                'review_assignment.score',
                'review_assignment.rate_originality',
                'review_assignment.rate_literature_depth',
                'review_assignment.rate_methodology',
                'review_assignment.rate_novelty',
                'review_assignment.rate_clarity',
                'review_assignment.rate_contribution',
                'review_assignment.accept_presentation',
                'review_assignment.overall_comments',
                'review_assignment.comments_to_author',
                'review_assignment.submitted_at'
            )
            ->get();


        return view('committee.papers.show', compact('paper', 'authors', 'conference_id', 'affiliation', 'avgScore', 'reviews'));
    }


    // 3️⃣ Abstract decision (Accept / Reject)
    public function decideAbstract(Request $request, $conference_id, $paper_id)
    {
        $request->validate([
            'decision' => 'required|in:Accepted,Rejected',
        ]);

        $abstract = DB::table('abstract_submission')
            ->join('paper', 'paper.paper_id', '=', 'abstract_submission.paper_id')
            ->where('paper.paper_id', $paper_id)
            ->where('paper.conference_id', $conference_id)
            ->select('abstract_submission.abstract_status')
            ->first();

        if (!$abstract || $abstract->abstract_status !== 'Submitted') {
            return back()->with('error', 'Only submitted abstracts can be decided.');
        }

        DB::table('abstract_submission')
            ->where('paper_id', $paper_id)
            ->update([
                'abstract_status' => $request->decision,
                'updated_at' => now(),
            ]);

        // Create notification for the author
        $paper = DB::table('paper')->where('paper_id', $paper_id)->first();
        $correspondingAuthor = DB::table('paper_author')
            ->where('paper_id', $paper_id)
            ->where('is_corresponding_author', true)
            ->whereNotNull('participant_id')
            ->first();

        if ($paper && $correspondingAuthor) {
            $conference = DB::table('conference')->where('conference_id', $conference_id)->first();
            $statusLabel = strtolower($request->decision);
            DB::table('notification')->insert([
                'content' => "Your abstract for \"{$paper->title}\" has been {$statusLabel} in {$conference->name}.",
                'link' => route('submissions.show', $paper_id),
                'status' => 'unread',
                'conference_id' => $conference_id,
                'participant_id' => $correspondingAuthor->participant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Broadcast real-time
            broadcast(new NotificationSent($correspondingAuthor->participant_id, "Your abstract for \"{$paper->title}\" has been {$statusLabel} in {$conference->name}."));
        }

        return back()->with('success', 'Abstract decision saved.');
    }

    public function decideFullPaper(Request $request, $conference_id, $paper_id)
    {
        $request->validate([
            'decision' => 'required|in:Accepted,Rejected',
        ]);

        // Ensure paper belongs to this conference
        $paper = DB::table('paper')
            ->where('paper_id', $paper_id)
            ->where('conference_id', $conference_id)
            ->first();

        if (!$paper) {
            abort(404, 'Paper not found in this conference.');
        }

        // Must have a full paper submission and must be Submitted to decide
        $full = DB::table('fullpaper_submission')
            ->where('paper_id', $paper_id)
            ->select('fullpaper_status', 'file_path')
            ->first();

        if (!$full) {
            return back()->with('error', 'No full paper submitted yet.');
        }

        if (empty($full->file_path)) {
            return back()->with('error', 'Full paper file is missing.');
        }

        if ($full->fullpaper_status !== 'Submitted') {
            return back()->with('error', 'Only submitted full papers can be decided.');
        }

        DB::table('fullpaper_submission')
            ->where('paper_id', $paper_id)
            ->update([
                'fullpaper_status' => $request->decision,
                'updated_at' => now(),
            ]);

        // Create notification for the author
        $correspondingAuthor = DB::table('paper_author')
            ->where('paper_id', $paper_id)
            ->where('is_corresponding_author', true)
            ->whereNotNull('participant_id')
            ->first();

        if ($paper && $correspondingAuthor) {
            $conference = DB::table('conference')->where('conference_id', $conference_id)->first();
            $statusLabel = strtolower($request->decision);
            DB::table('notification')->insert([
                'content' => "Your full paper for \"{$paper->title}\" has been {$statusLabel} in {$conference->name}.",
                'link' => route('submissions.show', $paper_id),
                'status' => 'unread',
                'conference_id' => $conference_id,
                'participant_id' => $correspondingAuthor->participant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Broadcast real-time
            broadcast(new NotificationSent($correspondingAuthor->participant_id, "Your full paper for \"{$paper->title}\" has been {$statusLabel} in {$conference->name}."));
        }

        return back()->with('success', 'Full paper decision saved.');
    }


    public function reviews($conference_id, $paper_id)
    {
        // Basic paper info
        $paper = DB::table('paper')
            ->join('conference', 'conference.conference_id', '=', 'paper.conference_id')
            ->leftJoin('track', 'track.track_id', '=', 'paper.track_id')
            ->where('paper.paper_id', $paper_id)
            ->where('paper.conference_id', $conference_id)
            ->select(
                'paper.paper_id',
                'paper.title',
                'paper.keywords',
                'conference.name as conference_name',
                DB::raw('COALESCE(track.name, "-") as track_name')
            )
            ->first();

        if (!$paper)
            abort(404);

        // Reviews for this paper
        $reviews = DB::table('review_assignment')
            ->join('reviewer', 'reviewer.reviewer_id', '=', 'review_assignment.reviewer_id')
            ->join('fullpaper_submission', 'fullpaper_submission.fullpaper_id', '=', 'review_assignment.fullpaper_id')
            ->where('fullpaper_submission.paper_id', $paper_id)
            ->select(
                'review_assignment.review_id',
                'review_assignment.score',
                'review_assignment.review_status',
                'review_assignment.accept_presentation',
                'review_assignment.created_at as assigned_date',
                'review_assignment.submitted_at',
                'reviewer.name as reviewer_name',
                'reviewer.area as reviewer_area'
            )
            ->orderByDesc('review_assignment.submitted_at')
            ->get();

        // Average score (only Submitted)
        $avgScore = DB::table('review_assignment')
            ->join('fullpaper_submission', 'fullpaper_submission.fullpaper_id', '=', 'review_assignment.fullpaper_id')
            ->where('fullpaper_submission.paper_id', $paper_id)
            ->where('review_assignment.review_status', 'Submitted')
            ->avg('score');

        // Authors list
        $authors = DB::table('paper_author')
            ->where('paper_id', $paper_id)
            ->orderByDesc('is_corresponding_author')
            ->get();


        return view('committee.papers.reviews', compact('conference_id', 'paper', 'reviews', 'avgScore', 'authors'));
    }




}
