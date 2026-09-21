<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// Helper: create super admin and return session array
function superAdminSession(): array
{
    $id = DB::table('super_admin')->insertGetId([
        'name'       => 'Super Admin',
        'email'      => 'sa@test.com',
        'password'   => Hash::make('pass'),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return ['user_id' => $id, 'user_type' => 'super_admin'];
}

// Helper: seed required RBAC
function seedRbac(): void
{
    if (!DB::table('role')->where('role_name', 'CONFERENCE_ADMIN')->exists()) {
        DB::table('role')->insert([
            'role_name'  => 'CONFERENCE_ADMIN',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

// Helper: insert a pending conference request
function insertPendingRequest(): int
{
    return DB::table('conference_request')->insertGetId([
        'conference_title' => 'Test Conference',
        'org_name'         => 'Test Org',
        'org_type'         => 'University',
        'org_address'      => '123 Street',
        'applicant_name'   => 'Applicant',
        'applicant_email'  => 'applicant@test.com',
        'applicant_phone'  => '0123456789',
        'status'           => 'Pending',
        'submitted_date'   => now(),
        'superadmin_id'    => 1,
    ]);
}

// --- Public: Show Conference Request Form ---

it('shows the conference request form', function () {
    $response = $this->get('/conference-request');
    $response->assertStatus(200);
});

// --- Public: Submit Conference Request ---

it('submits a conference request successfully', function () {
    $response = $this->post('/conference-request', [
        'conference_title' => 'Annual Tech Conference',
        'org_name'         => 'Tech Corp',
        'org_type'         => 'Company',
        'org_address'      => '456 Tech Ave',
        'applicant_name'   => 'John Applicant',
        'applicant_email'  => 'john@corp.com',
        'applicant_phone'  => '0198765432',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');
    $this->assertDatabaseHas('conference_request', ['applicant_email' => 'john@corp.com']);
});

it('validates required fields when submitting a conference request', function () {
    $response = $this->post('/conference-request', []);
    $response->assertSessionHasErrors(['conference_title', 'org_name', 'org_type', 'org_address', 'applicant_name', 'applicant_email', 'applicant_phone']);
});

// --- Super Admin: List Requests ---

it('super admin can view the conference request list', function () {
    $session = superAdminSession();

    $response = $this->withSession($session)->get('/superadmin/conference-requests');
    $response->assertStatus(200);
});

it('guests cannot view the conference request list', function () {
    $response = $this->get('/superadmin/conference-requests');
    $response->assertStatus(302); // redirect to login
});

// --- Super Admin: Approve Request ---

it('super admin can approve a pending conference request', function () {
    seedRbac();
    $session = superAdminSession();
    $id = insertPendingRequest();

    $response = $this->withSession($session)
        ->post("/superadmin/conference-requests/{$id}/approve");

    $response->assertRedirect();
    $this->assertDatabaseHas('conference_request', ['request_id' => $id, 'status' => 'Approved']);
    $this->assertDatabaseHas('conference', ['name' => 'Test Conference']);
});

it('cannot approve a request that is already processed', function () {
    seedRbac();
    $session = superAdminSession();

    $id = DB::table('conference_request')->insertGetId([
        'conference_title' => 'Already Processed',
        'org_name'         => 'Org',
        'org_type'         => 'University',
        'org_address'      => 'Address',
        'applicant_name'   => 'Name',
        'applicant_email'  => 'already@test.com',
        'applicant_phone'  => '0100000000',
        'status'           => 'Approved',
        'submitted_date'   => now(),
        'superadmin_id'    => 1,
    ]);

    $response = $this->withSession($session)
        ->post("/superadmin/conference-requests/{$id}/approve");

    $response->assertStatus(400);
});

// --- Super Admin: Reject Request ---

it('super admin can reject a pending conference request', function () {
    $session = superAdminSession();
    $id = insertPendingRequest();

    $response = $this->withSession($session)
        ->post("/superadmin/conference-requests/{$id}/reject");

    $response->assertRedirect();
    $this->assertDatabaseHas('conference_request', ['request_id' => $id, 'status' => 'Rejected']);
});
