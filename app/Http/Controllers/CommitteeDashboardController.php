<?php

namespace App\Http\Controllers;

use App\Services\CommitteeService;

class CommitteeDashboardController extends Controller
{
    public function __construct(
        protected CommitteeService $committeeService
    ) {}

    public function show($conference_id)
    {
        $stats = $this->committeeService->getDashboardStats($conference_id);

        return view('committee.dashboard', [
            'conference' => $stats['conference'],
            'totalPapers' => $stats['totalPapers'],
            'abstractCounts' => $stats['abstractCounts'],
            'fullpaperCounts' => $stats['fullpaperCounts'],
            'cameraReadySubmitted' => $stats['cameraReadySubmitted'],
            'presenterCounts' => $stats['presenterCounts'],
        ]);
    }
}
