<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// Helper: create a reviewer with REVIEW_PAPER permission and return session
function reviewerSession(): array
{
    if (!DB::table('permission')->where('permission_name', 'REVIEW_PAPER')->exists()) {
        DB::table('permission')->insert(['permission_name' => 'REVIEW_PAPER', 'created_at' => now(), 'updated_at' => now()]);
    }

    $roleId = DB::table('role')->insertGetId([
        'role_name'  => 'REVIEWER_' . uniqid(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $permId = DB::table('permission')->where('permission_name', 'REVIEW_PAPER')->value('permission_id');
    DB::table('role_permission')->insert(['role_id' => $roleId, 'permission_id' => $permId, 'created_at' => now(), 'updated_at' => now()]);

    $reviewerId = DB::table('reviewer')->insertGetId([
        'name'       => 'Test Reviewer',
        'email'      => 'reviewer' . uniqid() . '@test.com',
        'password'   => Hash::make('pass'),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('user_role')->insert([
        'user_type'     => 'reviewer',
        'user_id'       => $reviewerId,
        'role_id'       => $roleId,
        'assigned_date' => now(),
        'created_at'    => now(),
        'updated_at'    => now(),
    ]);

    return ['user_id' => $reviewerId, 'user_type' => 'reviewer', '_id' => $reviewerId];
}

// Helper: create a review assignment for the reviewer
function createReviewAssignment(int $reviewerId): int
{
    $conferenceId = DB::table('conference')->insertGetId([
        'name'                => 'Review Conference',
        'conference_email'    => 'rc@test.com',
        'conference_phone'    => '0100000001',
        'secretariat_address' => 'Addr',
        'chairman_name'       => 'Chair',
        'chairman_email'      => 'chair@test.com',
        'conference_date'     => now()->addMonths(3),
        'conference_venue'    => 'Hall',
        'conference_status'   => 'active',
        'created_at'          => now(),
        'updated_at'          => now(),
    ]);

    $trackId = DB::table('track')->insertGetId([
        'conference_id' => $conferenceId,
        'created_at'    => now(),
        'updated_at'    => now(),
    ]);

    $paperId = DB::table('paper')->insertGetId([
        'title'         => 'Paper To Review',
        'keywords'      => 'test',
        'conference_id' => $conferenceId,
        'track_id'      => $trackId,
        'created_at'    => now(),
        'updated_at'    => now(),
    ]);

    return DB::table('review_assignment')->insertGetId([
        'reviewer_id'       => $reviewerId,
        'paper_id'          => $paperId,
        'assignment_status' => 'Assigned',
        'assigned_date'     => now(),
        'created_at'        => now(),
        'updated_at'        => now(),
    ]);
}

// --- My Reviews List ---

it('reviewer can view their review list', function () {
    $session = reviewerSession();

    $response = $this->withSession($session)->get('/reviewer/my-reviews');
    $response->assertStatus(200);
});

it('guests cannot access reviewer reviews', function () {
    $response = $this->get('/reviewer/my-reviews');
    $response->assertStatus(302);
});

// --- Submit Review ---

it('reviewer can submit a review', function () {
    $session = reviewerSession();
    $assignmentId = createReviewAssignment($session['_id']);

    $response = $this->withSession($session)
        ->post("/reviewer/my-reviews/{$assignmentId}", [
            'score'    => 8,
            'comments' => 'Good paper with solid methodology.',
        ]);

    $response->assertRedirect();
});

// --- Reviewer Profile ---

it('reviewer can view their profile', function () {
    $session = reviewerSession();

    $response = $this->withSession($session)->get('/reviewer/profile');
    $response->assertStatus(200);
});

it('reviewer can update their profile', function () {
    $session = reviewerSession();

    $response = $this->withSession($session)->post('/reviewer/profile', [
        'name'        => 'Updated Reviewer Name',
        'affiliation' => 'UTM',
    ]);

    $response->assertRedirect();
});

it('reviewer can change their password', function () {
    $session = reviewerSession();

    // Update the password of the reviewer to a known value
    DB::table('reviewer')->where('reviewer_id', $session['_id'])->update([
        'password' => Hash::make('oldpassword'),
    ]);

    $response = $this->withSession($session)->post('/reviewer/profile/password', [
        'current_password'      => 'oldpassword',
        'password'              => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    $response->assertRedirect();
});
