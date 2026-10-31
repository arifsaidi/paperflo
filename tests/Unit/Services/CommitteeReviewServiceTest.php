<?php

use App\Models\Paper;
use App\Models\Track;
use App\Services\CommitteeReviewService;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

beforeEach(function () {
    $this->conference = createTestConference([
        'name' => 'AI & Robotics Conference 2026',
        'conference_status' => 'active',
    ]);

    $this->track = Track::create([
        'conference_id' => $this->conference->conference_id,
        'name' => 'Machine Learning',
    ]);

    $this->paper = Paper::create([
        'title' => 'Deep Learning Advances',
        'conference_id' => $this->conference->conference_id,
        'track_id' => $this->track->track_id,
    ]);

    $this->service = app(CommitteeReviewService::class);
});

test('getPaperReviewsData loads paper details, authors, eligible reviewers, assignments and avgScore', function () {
    // 1. Paper author
    DB::table('paper_author')->insert([
        'name' => 'Dr. Alan Turing',
        'author_name' => 'Dr. Alan Turing',
        'author_email' => 'turing@ai.test',
        'is_corresponding_author' => true,
        'paper_id' => $this->paper->paper_id,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // 2. Fullpaper submission
    $fullpaperId = DB::table('fullpaper_submission')->insertGetId([
        'paper_id' => $this->paper->paper_id,
        'file_path' => 'uploads/fullpapers/sample.pdf',
        'status' => 'Submitted',
        'fullpaper_status' => 'Pending Review',
        'submitted_date' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // 3. Reviewer invited & accepted for conference
    $reviewerId = DB::table('reviewer')->insertGetId([
        'name' => 'Prof. Ada Lovelace',
        'email' => 'ada@math.test',
        'password' => bcrypt('secret'),
        'area' => 'Algorithms',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('conference_reviewer_assignment')->insert([
        'conference_id' => $this->conference->conference_id,
        'reviewer_id' => $reviewerId,
        'reviewer_type' => 'Internal',
        'invite_status' => 'Accepted',
        'invite_date' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // 4. Review assignment with submitted score
    DB::table('review_assignment')->insert([
        'fullpaper_id' => $fullpaperId,
        'reviewer_id' => $reviewerId,
        'score' => 85,
        'comment' => 'Excellent work.',
        'review_status' => 'Submitted',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $data = $this->service->getPaperReviewsData($this->conference->conference_id, $this->paper->paper_id);

    expect($data['paper']->paper_id)->toBe($this->paper->paper_id);
    expect($data['paper']->track_name)->toBe('Machine Learning');
    expect($data['paper']->conference_name)->toBe('AI & Robotics Conference 2026');
    expect($data['authors'])->toHaveCount(1);
    expect($data['authors']->first()->author_name)->toBe('Dr. Alan Turing');
    expect($data['fullpaperId'])->toBe($fullpaperId);
    expect($data['reviewers'])->toHaveCount(1);
    expect($data['reviewers']->first()->name)->toBe('Prof. Ada Lovelace');
    expect($data['assignments'])->toHaveCount(1);
    expect($data['avgScore'])->toBe(85.0);
});

test('getPaperReviewsData aborts 404 when paper not in conference', function () {
    $otherConference = createTestConference(['name' => 'Other Conf']);

    expect(fn () => $this->service->getPaperReviewsData($otherConference->conference_id, $this->paper->paper_id))
        ->toThrow(HttpException::class);
});

test('calculateAverageScore computes rounded average for submitted reviews and returns null for empty', function () {
    $empty = collect();
    expect($this->service->calculateAverageScore($empty))->toBeNull();

    $assignments = collect([
        (object) ['review_status' => 'Submitted', 'score' => 80],
        (object) ['review_status' => 'Submitted', 'score' => 90],
        (object) ['review_status' => 'Assigned', 'score' => 0], // ignored
    ]);

    expect($this->service->calculateAverageScore($assignments))->toBe(85.0);
});

test('getReviewDetailsData loads review details and paper info', function () {
    $fullpaperId = DB::table('fullpaper_submission')->insertGetId([
        'paper_id' => $this->paper->paper_id,
        'file_path' => 'uploads/fullpapers/sample.pdf',
        'status' => 'Submitted',
        'fullpaper_status' => 'Pending Review',
        'submitted_date' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $reviewerId = DB::table('reviewer')->insertGetId([
        'name' => 'Prof. Claude Shannon',
        'email' => 'shannon@info.test',
        'password' => bcrypt('secret'),
        'area' => 'Information Theory',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $reviewId = DB::table('review_assignment')->insertGetId([
        'fullpaper_id' => $fullpaperId,
        'reviewer_id' => $reviewerId,
        'score' => 92,
        'comment' => 'Groundbreaking contribution.',
        'review_status' => 'Submitted',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $data = $this->service->getReviewDetailsData(
        $this->conference->conference_id,
        $this->paper->paper_id,
        $reviewId
    );

    expect($data['paper']->paper_id)->toBe($this->paper->paper_id);
    expect($data['review']->review_id)->toBe($reviewId);
    expect($data['review']->score)->toBe(92);
    expect($data['review']->fullpaper_file_path)->toBe('uploads/fullpapers/sample.pdf');
    expect($data['readonly'])->toBeTrue();
});

test('assignReviewer successfully assigns reviewer to paper fullpaper', function () {
    $fullpaperId = DB::table('fullpaper_submission')->insertGetId([
        'paper_id' => $this->paper->paper_id,
        'file_path' => 'uploads/fullpapers/paper.pdf',
        'status' => 'Submitted',
        'fullpaper_status' => 'Pending Review',
        'submitted_date' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $reviewerId = DB::table('reviewer')->insertGetId([
        'name' => 'Dr. Geoffrey Hinton',
        'email' => 'hinton@toronto.test',
        'password' => bcrypt('secret'),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('conference_reviewer_assignment')->insert([
        'conference_id' => $this->conference->conference_id,
        'reviewer_id' => $reviewerId,
        'reviewer_type' => 'Internal',
        'invite_status' => 'Accepted',
        'invite_date' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $reviewId = $this->service->assignReviewer(
        $this->conference->conference_id,
        $this->paper->paper_id,
        $reviewerId
    );

    expect($reviewId)->toBeGreaterThan(0);

    $assignment = DB::table('review_assignment')->where('review_id', $reviewId)->first();
    expect($assignment)->not->toBeNull();
    expect($assignment->review_status)->toBe('Assigned');
    expect($assignment->reviewer_id)->toBe($reviewerId);
    expect($assignment->fullpaper_id)->toBe($fullpaperId);
});

test('assignReviewer rejects assignment when conditions fail', function () {
    $fullpaperId = DB::table('fullpaper_submission')->insertGetId([
        'paper_id' => $this->paper->paper_id,
        'file_path' => 'uploads/fullpapers/paper.pdf',
        'status' => 'Submitted',
        'fullpaper_status' => 'Pending Review',
        'submitted_date' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $reviewerId = DB::table('reviewer')->insertGetId([
        'name' => 'Dr. Uninvited',
        'email' => 'uninvited@reviewer.test',
        'password' => bcrypt('secret'),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // 1. Reviewer not invited or accepted
    expect(fn () => $this->service->assignReviewer($this->conference->conference_id, $this->paper->paper_id, $reviewerId))
        ->toThrow(InvalidArgumentException::class, 'Reviewer is not invited to this conference.');

    // Invite reviewer
    DB::table('conference_reviewer_assignment')->insert([
        'conference_id' => $this->conference->conference_id,
        'reviewer_id' => $reviewerId,
        'reviewer_type' => 'Internal',
        'invite_status' => 'Accepted',
        'invite_date' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // 2. Invalid paper ID
    expect(fn () => $this->service->assignReviewer($this->conference->conference_id, 99999, $reviewerId))
        ->toThrow(InvalidArgumentException::class, 'Invalid paper for this conference.');

    // 3. Assign reviewer successfully once
    $this->service->assignReviewer($this->conference->conference_id, $this->paper->paper_id, $reviewerId);

    // 4. Duplicate assignment prevention
    expect(fn () => $this->service->assignReviewer($this->conference->conference_id, $this->paper->paper_id, $reviewerId))
        ->toThrow(InvalidArgumentException::class, 'Reviewer already assigned to this paper.');
});
