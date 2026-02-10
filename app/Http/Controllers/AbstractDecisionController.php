<?php

namespace App\Http\Controllers;

use App\Events\NotificationSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AbstractDecisionController extends Controller
{
    public function index($conference_id)
    {
        $conference = DB::table('conference')
            ->where('conference_id', $conference_id)
            ->first();

        if (!$conference) {
            abort(404, 'Conference not found.');
        }

        // Show only abstracts that were submitted or already decided
        $items = DB::table('paper')
            ->join('abstract_submission', 'abstract_submission.paper_id', '=', 'paper.paper_id')
            ->leftJoin('track', 'track.track_id', '=', 'paper.track_id')
            ->where('paper.conference_id', $conference_id)
            ->whereIn('abstract_submission.abstract_status', ['Submitted', 'Accepted', 'Rejected'])
            ->select(
                'paper.paper_id',
                'paper.title',
                'paper.keywords',
                DB::raw('COALESCE(track.name, CONCAT("Track #", paper.track_id)) as track_name'),
                'abstract_submission.abstract_status',
                'abstract_submission.submitted_date'
            )
            ->orderByDesc('abstract_submission.submitted_date')
            ->get();

        return view('committee.abstract_decisions', compact('conference', 'items'));
    }

    public function decide(Request $request, $conference_id, $paper_id)
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

        // Only decide if abstract is currently Submitted
        $abstract = DB::table('abstract_submission')
            ->where('paper_id', $paper_id)
            ->first();

        if (!$abstract) {
            abort(404, 'Abstract submission not found.');
        }

        if ($abstract->abstract_status !== 'Submitted') {
            return back()->with('error', 'Only submitted abstracts can be decided.');
        }

        DB::table('abstract_submission')
            ->where('paper_id', $paper_id)
            ->update([
                'abstract_status' => $request->decision,
                'updated_at' => now(),
            ]);

        // Send notification to corresponding author
        $author = DB::table('paper_author')
            ->where('paper_id', $paper_id)
            ->where('is_corresponding_author', true)
            ->first();

        if ($author && $author->participant_id) {
            $message = "Your abstract for '{$paper->title}' has been {$request->decision}.";

            // Save to database
            DB::table('notification')->insert([
                'content' => $message,
                'status' => 'unread',
                'conference_id' => $conference_id,
                'participant_id' => $author->participant_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Broadcast real-time
            broadcast(new NotificationSent($author->participant_id, $message));
        }

        return back()->with('success', 'Decision saved.');
    }
}
