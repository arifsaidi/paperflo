<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class CommitteeReviewService
{
    /**
     * Get all review data needed for a paper in a conference.
     *
     * @param int $conferenceId
     * @param int $paperId
     * @return array
     */
    public function getPaperReviewsData(int $conferenceId, int $paperId): array
    {
        $paper = $this->getPaperWithTrack($conferenceId, $paperId);

        if (!$paper) {
            abort(404, 'Paper not found in this conference.');
        }

        $authors = $this->getPaperAuthors($paperId);
        $fullpaper = $this->getFullpaperSubmission($paperId);
        $fullpaperId = $fullpaper?->fullpaper_id;

        $reviewers = $this->getAcceptedConferenceReviewers($conferenceId);
        $assignments = $fullpaperId ? $this->getReviewAssignments($fullpaperId) : collect();
        $avgScore = $this->calculateAverageScore($assignments);

        return [
            'conference_id' => $conferenceId,
            'paper' => $paper,
            'authors' => $authors,
            'fullpaperId' => $fullpaperId,
            'reviewers' => $reviewers,
            'assignments' => $assignments,
            'avgScore' => $avgScore,
        ];
    }

    /**
     * Retrieve paper with associated conference and track information.
     */
    public function getPaperWithTrack(int $conferenceId, int $paperId): ?object
    {
        return DB::table('paper')
            ->join('conference', 'conference.conference_id', '=', 'paper.conference_id')
            ->leftJoin('track', 'track.track_id', '=', 'paper.track_id')
            ->where('paper.paper_id', $paperId)
            ->where('paper.conference_id', $conferenceId)
            ->select([
                'paper.paper_id',
                'paper.title',
                'paper.keywords',
                'paper.track_id',
                DB::raw('COALESCE(track.name, "-") as track_name'),
                'conference.name as conference_name',
            ])
            ->first();
    }

    /**
     * Retrieve authors for a paper ordered by corresponding author status.
     */
    public function getPaperAuthors(int $paperId): Collection
    {
        return DB::table('paper_author')
            ->where('paper_id', $paperId)
            ->orderByDesc('is_corresponding_author')
            ->get();
    }

    /**
     * Retrieve fullpaper submission for a given paper.
     */
    public function getFullpaperSubmission(int $paperId): ?object
    {
        return DB::table('fullpaper_submission')
            ->where('paper_id', $paperId)
            ->first();
    }

    /**
     * Retrieve list of reviewers who have accepted invitation for this conference.
     */
    public function getAcceptedConferenceReviewers(int $conferenceId): Collection
    {
        return DB::table('conference_reviewer_assignment')
            ->join('reviewer', 'reviewer.reviewer_id', '=', 'conference_reviewer_assignment.reviewer_id')
            ->where('conference_reviewer_assignment.conference_id', $conferenceId)
            ->where('conference_reviewer_assignment.invite_status', 'Accepted')
            ->select([
                'reviewer.reviewer_id',
                'reviewer.name',
                'reviewer.area',
                'reviewer.email',
            ])
            ->orderBy('reviewer.name')
            ->get();
    }

    /**
     * Retrieve all review assignments for a specific fullpaper.
     */
    public function getReviewAssignments(int $fullpaperId): Collection
    {
        return DB::table('review_assignment')
            ->join('reviewer', 'reviewer.reviewer_id', '=', 'review_assignment.reviewer_id')
            ->where('review_assignment.fullpaper_id', $fullpaperId)
            ->select([
                'review_assignment.review_id',
                'review_assignment.review_status',
                'review_assignment.score',
                'review_assignment.created_at as assigned_date',
                'reviewer.reviewer_id',
                'reviewer.name as reviewer_name',
                'reviewer.area as reviewer_area',
            ])
            ->orderByDesc('review_assignment.review_id')
            ->get();
    }

    /**
     * Calculate average score for submitted reviews.
     */
    public function calculateAverageScore(Collection $assignments): ?float
    {
        $submitted = $assignments->where('review_status', 'Submitted');

        if ($submitted->count() > 0) {
            return round((float) $submitted->avg('score'), 2);
        }

        return null;
    }

    /**
     * Get review details view data for a specific review on a paper.
     *
     * @param int $conferenceId
     * @param int $paperId
     * @param int $reviewId
     * @return array
     */
    public function getReviewDetailsData(int $conferenceId, int $paperId, int $reviewId): array
    {
        $paper = DB::table('paper')
            ->leftJoin('track', 'track.track_id', '=', 'paper.track_id')
            ->where('paper.paper_id', $paperId)
            ->where('paper.conference_id', $conferenceId)
            ->select([
                'paper.paper_id',
                'paper.title',
                'paper.keywords',
                DB::raw('COALESCE(track.name, "-") as track_name'),
            ])
            ->first();

        if (!$paper) {
            abort(404, 'Paper not found in this conference.');
        }

        $fullpaper = $this->getFullpaperSubmission($paperId);
        if (!$fullpaper) {
            abort(404, 'Full paper not found for this paper.');
        }

        $review = $this->getReviewDetails($conferenceId, $paperId, $reviewId);
        if (!$review) {
            abort(404, 'Review not found.');
        }

        return [
            'review' => $review,
            'paper' => $paper,
            'conference_id' => $conferenceId,
            'readonly' => true,
        ];
    }

    /**
     * Retrieve single review record with reviewer, paper, conference, and fullpaper details.
     */
    public function getReviewDetails(int $conferenceId, int $paperId, int $reviewId): ?object
    {
        return DB::table('review_assignment')
            ->join('reviewer', 'reviewer.reviewer_id', '=', 'review_assignment.reviewer_id')
            ->join('fullpaper_submission', 'fullpaper_submission.fullpaper_id', '=', 'review_assignment.fullpaper_id')
            ->join('paper', 'paper.paper_id', '=', 'fullpaper_submission.paper_id')
            ->join('conference', 'conference.conference_id', '=', 'paper.conference_id')
            ->where('review_assignment.review_id', $reviewId)
            ->where('paper.paper_id', $paperId)
            ->where('paper.conference_id', $conferenceId)
            ->select([
                'review_assignment.*',
                'paper.paper_id',
                'paper.title',
                'paper.keywords',
                'conference.name as conference_name',
                'fullpaper_submission.file_path as fullpaper_file_path',
            ])
            ->first();
    }

    /**
     * Assign a reviewer to a paper's fullpaper submission.
     *
     * @param int $conferenceId
     * @param int $paperId
     * @param int $reviewerId
     * @return int Created review_id
     * @throws InvalidArgumentException
     */
    public function assignReviewer(int $conferenceId, int $paperId, int $reviewerId): int
    {
        // 1. Ensure paper belongs to conference
        $paper = DB::table('paper')
            ->where('paper_id', $paperId)
            ->where('conference_id', $conferenceId)
            ->first();

        if (!$paper) {
            throw new InvalidArgumentException('Invalid paper for this conference.');
        }

        // 2. Ensure fullpaper submission exists
        $fullpaper = $this->getFullpaperSubmission($paperId);

        if (!$fullpaper) {
            throw new InvalidArgumentException('Full paper not found.');
        }

        // 3. Ensure reviewer is invited & accepted for this conference
        $invited = DB::table('conference_reviewer_assignment')
            ->where('conference_id', $conferenceId)
            ->where('reviewer_id', $reviewerId)
            ->where('invite_status', 'Accepted')
            ->exists();

        if (!$invited) {
            throw new InvalidArgumentException('Reviewer is not invited to this conference.');
        }

        // 4. Prevent duplicate assignment
        $alreadyAssigned = DB::table('review_assignment')
            ->where('fullpaper_id', $fullpaper->fullpaper_id)
            ->where('reviewer_id', $reviewerId)
            ->exists();

        if ($alreadyAssigned) {
            throw new InvalidArgumentException('Reviewer already assigned to this paper.');
        }

        // 5. Insert review assignment
        return DB::table('review_assignment')->insertGetId([
            'fullpaper_id' => $fullpaper->fullpaper_id,
            'reviewer_id' => $reviewerId,
            'score' => 0,
            'review_status' => 'Assigned',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
