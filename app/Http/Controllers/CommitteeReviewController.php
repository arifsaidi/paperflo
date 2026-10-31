<?php

namespace App\Http\Controllers;

use App\Services\CommitteeReviewService;
use InvalidArgumentException;

class CommitteeReviewController extends Controller
{
    public function __construct(
        protected CommitteeReviewService $reviewService
    ) {}

    /**
     * Display paper review overview including authors, reviewers, and assignments.
     */
    public function paperReviews($conference_id, $paper_id)
    {
        $data = $this->reviewService->getPaperReviewsData((int) $conference_id, (int) $paper_id);

        return view('committee.reviews.paper', $data);
    }

    /**
     * Display detailed information and score of a specific review.
     */
    public function reviewDetails($conference_id, $paper_id, $review_id)
    {
        $data = $this->reviewService->getReviewDetailsData((int) $conference_id, (int) $paper_id, (int) $review_id);

        return view('committee.reviews.show', $data);
    }

    /**
     * Assign an eligible reviewer to a paper's fullpaper submission.
     */
    public function assignReviewer($conference_id, $paper_id, $reviewer_id)
    {
        try {
            $this->reviewService->assignReviewer((int) $conference_id, (int) $paper_id, (int) $reviewer_id);

            return back()->with('success', 'Reviewer assigned successfully.');
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
