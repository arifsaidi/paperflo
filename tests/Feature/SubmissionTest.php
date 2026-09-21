<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// Helper: create a participant and return session + id
function participantSession(): array
{
    $id = DB::table('participant')->insertGetId([
        'name'              => 'Test Participant',
        'email'             => 'participant@test.com',
        'password'          => Hash::make('pass'),
        'registration_date' => now(),
        'created_at'        => now(),
        'updated_at'        => now(),
    ]);

    return ['user_id' => $id, 'user_type' => 'participant', '_id' => $id];
}

// Helper: seed an active conference with a track and return conference_id, track_id
function seedActiveConference(): array
{
    $conferenceId = DB::table('conference')->insertGetId([
        'name'               => 'Active Conference',
        'conference_email'   => 'conf@test.com',
        'conference_phone'   => '0100000001',
        'secretariat_address'=> 'Addr',
        'chairman_name'      => 'Chair',
        'chairman_email'     => 'chair@test.com',
        'conference_date'    => now()->addMonths(3),
        'conference_venue'   => 'Hall',
        'conference_status'  => 'active',
        'created_at'         => now(),
        'updated_at'         => now(),
    ]);

    $trackId = DB::table('track')->insertGetId([
        'name'          => 'AI Track',
        'conference_id' => $conferenceId,
        'created_at'    => now(),
        'updated_at'    => now(),
    ]);

    return compact('conferenceId', 'trackId');
}

// Helper: register participant as author in a conference
function registerAsAuthor(int $participantId, int $conferenceId): void
{
    DB::table('conference_registration')->insert([
        'participant_id'      => $participantId,
        'conference_id'       => $conferenceId,
        'registration_type'   => 'author',
        'registration_status' => 'Completed',
        'registration_date'   => now(),
        'created_at'          => now(),
        'updated_at'          => now(),
    ]);
}

// Helper: create a paper with abstract_status
function seedPaper(int $participantId, int $conferenceId, int $trackId, string $status = 'Draft'): int
{
    $paperId = DB::table('paper')->insertGetId([
        'title'         => 'A Test Paper',
        'keywords'      => 'test, paper',
        'conference_id' => $conferenceId,
        'track_id'      => $trackId,
        'created_at'    => now(),
        'updated_at'    => now(),
    ]);

    DB::table('paper_author')->insert([
        'name'                     => 'Author Name',
        'author_name'              => 'Author Name',
        'author_email'             => 'participant@test.com',
        'is_corresponding_author'  => true,
        'paper_id'                 => $paperId,
        'participant_id'           => $participantId,
        'created_at'               => now(),
        'updated_at'               => now(),
    ]);

    DB::table('abstract_submission')->insert([
        'abstract_text'    => 'This is the abstract.',
        'status'           => 'ACTIVE',
        'abstract_status'  => $status,
        'submitted_date'   => now(),
        'paper_id'         => $paperId,
        'created_at'       => now(),
        'updated_at'       => now(),
    ]);

    return $paperId;
}

// --- Create Submission Form ---

it('shows the submission create form for registered authors', function () {
    $session = participantSession();
    $data = seedActiveConference();
    registerAsAuthor($session['_id'], $data['conferenceId']);

    $response = $this->withSession($session)
        ->get("/conferences/{$data['conferenceId']}/my-submissions/create");

    $response->assertStatus(200);
});

it('blocks non-authors from viewing the submission form', function () {
    $session = participantSession();
    $data = seedActiveConference();
    // NOT registered as author

    $response = $this->withSession($session)
        ->get("/conferences/{$data['conferenceId']}/my-submissions/create");

    $response->assertStatus(403);
});

// --- Store Submission (Draft) ---

it('saves a paper as draft', function () {
    $session = participantSession();
    $data = seedActiveConference();
    registerAsAuthor($session['_id'], $data['conferenceId']);

    $response = $this->withSession($session)
        ->post("/conferences/{$data['conferenceId']}/my-submissions", [
            'title'                       => 'My Draft Paper',
            'abstract_text'               => 'Abstract content here.',
            'track_id'                    => $data['trackId'],
            'keywords'                    => 'laravel, test',
            'corresponding_author_name'   => 'Test Participant',
            'corresponding_author_email'  => 'participant@test.com',
            'action'                      => 'draft',
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('paper', ['title' => 'My Draft Paper']);
    $this->assertDatabaseHas('abstract_submission', ['abstract_status' => 'Draft']);
});

// --- Store Submission (Submit) ---

it('submits an abstract successfully', function () {
    $session = participantSession();
    $data = seedActiveConference();
    registerAsAuthor($session['_id'], $data['conferenceId']);

    $response = $this->withSession($session)
        ->post("/conferences/{$data['conferenceId']}/my-submissions", [
            'title'                       => 'My Submitted Paper',
            'abstract_text'               => 'Abstract content here.',
            'track_id'                    => $data['trackId'],
            'keywords'                    => 'laravel, test',
            'corresponding_author_name'   => 'Test Participant',
            'corresponding_author_email'  => 'participant@test.com',
            'action'                      => 'submit',
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('abstract_submission', ['abstract_status' => 'Submitted']);
});

// --- Update Draft ---

it('updates a draft paper', function () {
    $session = participantSession();
    $data = seedActiveConference();
    registerAsAuthor($session['_id'], $data['conferenceId']);
    $paperId = seedPaper($session['_id'], $data['conferenceId'], $data['trackId'], 'Draft');

    $response = $this->withSession($session)
        ->post("/my-submissions/{$paperId}/update", [
            'title'         => 'Updated Title',
            'abstract_text' => 'Updated abstract.',
            'keywords'      => 'updated, keywords',
            'track_id'      => $data['trackId'],
            'action'        => 'update',
        ]);

    $response->assertRedirect(route('submissions.show', $paperId));
    $this->assertDatabaseHas('paper', ['paper_id' => $paperId, 'title' => 'Updated Title']);
});

it('cannot update a submitted abstract', function () {
    $session = participantSession();
    $data = seedActiveConference();
    registerAsAuthor($session['_id'], $data['conferenceId']);
    $paperId = seedPaper($session['_id'], $data['conferenceId'], $data['trackId'], 'Submitted');

    $response = $this->withSession($session)
        ->post("/my-submissions/{$paperId}/update", [
            'title'         => 'Should Fail',
            'abstract_text' => 'nope.',
            'keywords'      => 'x',
            'track_id'      => $data['trackId'],
            'action'        => 'update',
        ]);

    $response->assertStatus(403);
});

// --- Withdraw Abstract ---

it('withdraws a submitted abstract', function () {
    $session = participantSession();
    $data = seedActiveConference();
    registerAsAuthor($session['_id'], $data['conferenceId']);
    $paperId = seedPaper($session['_id'], $data['conferenceId'], $data['trackId'], 'Submitted');

    $response = $this->withSession($session)
        ->post("/my-submissions/{$paperId}/withdraw", [
            'reason' => 'I have decided to withdraw this paper due to conflicts.',
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('abstract_submission', ['paper_id' => $paperId, 'abstract_status' => 'Withdrawn']);
});

it('requires a reason when withdrawing', function () {
    $session = participantSession();
    $data = seedActiveConference();
    registerAsAuthor($session['_id'], $data['conferenceId']);
    $paperId = seedPaper($session['_id'], $data['conferenceId'], $data['trackId'], 'Submitted');

    $response = $this->withSession($session)
        ->post("/my-submissions/{$paperId}/withdraw", ['reason' => 'short']);

    $response->assertSessionHasErrors('reason');
});

// --- Guests Blocked ---

it('guests cannot access my-submissions', function () {
    $response = $this->get('/my-submissions/1');
    $response->assertStatus(302);
});
