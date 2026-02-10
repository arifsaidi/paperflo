<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class CommitteeDashboardController extends Controller
{
    public function show($conference_id)
    {
        // Conference info
        $conference = DB::table('conference')
            ->where('conference_id', $conference_id)
            ->select('conference_id', 'name')
            ->first();

        if (!$conference) {
            abort(404);
        }

        // Total papers
        $totalPapers = DB::table('paper')
            ->where('conference_id', $conference_id)
            ->count();

        // Abstract status counts
        $abstractCounts = DB::table('paper')
            ->leftJoin('abstract_submission', 'abstract_submission.paper_id', '=', 'paper.paper_id')
            ->where('paper.conference_id', $conference_id)
            ->selectRaw("
    COALESCE(abstract_submission.abstract_status, 'No Abstract') as status,
    COUNT(*) as total
")
            ->groupByRaw("COALESCE(abstract_submission.abstract_status, 'No Abstract')")

            ->orderBy('status')
            ->get();

        // Full paper status counts
        $fullpaperCounts = DB::table('paper')
            ->leftJoin('fullpaper_submission', 'fullpaper_submission.paper_id', '=', 'paper.paper_id')
            ->where('paper.conference_id', $conference_id)
            ->selectRaw("
                COALESCE(fullpaper_submission.fullpaper_status, 'No Full Paper') as status,
                COUNT(*) as total
            ")
            ->groupByRaw("COALESCE(fullpaper_submission.fullpaper_status, 'No Full Paper')")
            ->orderBy('status')
            ->get();

        // Camera-ready submitted (exists)
        $cameraReadySubmitted = DB::table('paper')
            ->join('cameraready_submission', 'cameraready_submission.paper_id', '=', 'paper.paper_id')
            ->where('paper.conference_id', $conference_id)
            ->count();

        // Presenter assignment/payment counts
        $presenterCounts = DB::table('paper')
            ->leftJoin('presenter_assignment', 'presenter_assignment.paper_id', '=', 'paper.paper_id')
            ->where('paper.conference_id', $conference_id)
            ->selectRaw("
                CASE
                    WHEN presenter_assignment.paper_id IS NULL THEN 'Not Assigned'
                    ELSE COALESCE(presenter_assignment.presenter_status, 'Assigned')
                END as status,
                COUNT(*) as total
            ")
            ->groupByRaw("CASE WHEN presenter_assignment.paper_id IS NULL THEN 'Not Assigned' ELSE COALESCE(presenter_assignment.presenter_status, 'Assigned') END")
            ->orderBy('status')
            ->get();

        return view('committee.dashboard', compact(
            'conference',
            'totalPapers',
            'abstractCounts',
            'fullpaperCounts',
            'cameraReadySubmitted',
            'presenterCounts'
        ));
    }
}
