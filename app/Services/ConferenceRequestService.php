<?php

namespace App\Services;

use App\Mail\ConferenceApproved;
use App\Models\Conference;
use App\Models\ConferenceAdmin;
use App\Models\ConferenceAdminAssignment;
use App\Models\ConferenceRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ConferenceRequestService
{
    /**
     * Submit a new conference request.
     */
    public function submitRequest(array $data, ?int $participantId = null, ?int $superadminId = null): ConferenceRequest
    {
        if (!$participantId) {
            $participantId = DB::table('participant')->where('email', $data['applicant_email'] ?? '')->value('participant_id')
                ?? DB::table('participant')->insertGetId([
                    'name' => $data['applicant_name'] ?? 'Applicant',
                    'email' => $data['applicant_email'] ?? ('applicant_' . uniqid() . '@example.com'),
                    'password' => Hash::make(Str::random(12)),
                    'phone_num' => $data['applicant_phone'] ?? null,
                    'registration_date' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
        }

        if (!$superadminId) {
            $superadminId = DB::table('super_admin')->value('superadmin_id')
                ?? DB::table('super_admin')->insertGetId([
                    'name' => 'System Administrator',
                    'email' => 'admin@paperflo.test',
                    'password' => Hash::make(Str::random(12)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
        }

        return ConferenceRequest::create([
            'conference_title' => $data['conference_title'],
            'org_name' => $data['org_name'],
            'org_type' => $data['org_type'],
            'org_address' => $data['org_address'],
            'applicant_name' => $data['applicant_name'],
            'applicant_email' => $data['applicant_email'],
            'applicant_phone' => $data['applicant_phone'],
            'status' => 'Pending',
            'submitted_date' => now(),
            'participant_id' => $participantId,
            'superadmin_id' => $superadminId,
        ]);
    }

    /**
     * Get all conference requests.
     */
    public function getAllRequests(): Collection
    {
        return ConferenceRequest::all();
    }

    /**
     * Get single request by ID.
     */
    public function getRequest(int $requestId): ?ConferenceRequest
    {
        return ConferenceRequest::find($requestId);
    }

    /**
     * Approve a conference request: creates conference, admin account, assignment, and role.
     */
    public function approveRequest(int $requestId): array
    {
        return DB::transaction(function () use ($requestId) {
            $req = ConferenceRequest::where('request_id', $requestId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($req->status !== 'Pending') {
                throw new InvalidArgumentException('Request has already been processed.');
            }

            // 1. Update Request Status
            $req->update(['status' => 'Approved']);

            // 2. Create Conference Model
            $conference = Conference::create([
                'name' => $req->conference_title,
                'conference_email' => $req->applicant_email,
                'conference_phone' => $req->applicant_phone,
                'secretariat_address' => $req->org_address,
                'chairman_name' => $req->applicant_name,
                'chairman_email' => $req->applicant_email,
                'conference_date' => now()->addMonths(6),
                'conference_venue' => 'TBD',
                'conference_status' => 'draft',
                'organizer_participant_id' => $req->participant_id,
            ]);

            // 3. Create or Update Conference Admin Account
            $email = $req->applicant_email;
            $plainPassword = Str::random(10);

            $admin = ConferenceAdmin::where('email', $email)->first();

            if (!$admin) {
                $admin = ConferenceAdmin::create([
                    'name' => $req->applicant_name,
                    'email' => $email,
                    'password' => Hash::make($plainPassword),
                ]);
            } else {
                $admin->update([
                    'password' => Hash::make($plainPassword),
                ]);
            }

            // 4. Assign Admin to Conference Model
            ConferenceAdminAssignment::create([
                'assigned_date' => now(),
                'access_level' => 'admin',
                'admin_id' => $admin->admin_id,
                'conference_id' => $conference->conference_id,
            ]);

            // 5. Assign Role (CONFERENCE_ADMIN)
            $roleId = DB::table('role')->where('role_name', 'CONFERENCE_ADMIN')->value('role_id');
            if ($roleId) {
                $exists = DB::table('user_role')
                    ->where('user_type', 'conference_admin')
                    ->where('user_id', $admin->admin_id)
                    ->where('role_id', $roleId)
                    ->exists();

                if (!$exists) {
                    DB::table('user_role')->insert([
                        'user_type' => 'conference_admin',
                        'user_id' => $admin->admin_id,
                        'role_id' => $roleId,
                        'assigned_date' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // 6. Send Approval Email
            try {
                Mail::to($email)->send(new ConferenceApproved(
                    $conference->name,
                    $email,
                    $plainPassword
                ));
            } catch (\Exception $e) {
                // Ignore email failure in dev/local
            }

            return [
                'conference' => $conference,
                'admin' => $admin,
                'password' => $plainPassword,
            ];
        });
    }

    /**
     * Reject a conference request.
     */
    public function rejectRequest(int $requestId): ConferenceRequest
    {
        $req = ConferenceRequest::findOrFail($requestId);

        if ($req->status !== 'Pending') {
            throw new InvalidArgumentException('Request has already been processed.');
        }

        $req->update(['status' => 'Rejected']);

        return $req;
    }
}
