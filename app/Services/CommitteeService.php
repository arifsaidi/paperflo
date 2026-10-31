<?php

namespace App\Services;

use App\Mail\ConferenceApproved;
use App\Models\Conference;
use App\Models\ConferenceAdmin;
use App\Models\ConferenceAdminAssignment;
use App\Models\Paper;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use InvalidArgumentException;

class CommitteeService
{
    /**
     * Get all administrators assigned to a conference with role information.
     */
    public function getConferenceAdmins(int $conferenceId)
    {
        return DB::table('conference_admin_assignment as caa')
            ->join('conference_admin as ca', 'ca.admin_id', '=', 'caa.admin_id')
            ->leftJoin('user_role as ur', function ($join) {
                $join->on('ur.user_id', '=', 'ca.admin_id')
                    ->where('ur.user_type', '=', 'conference_admin');
            })
            ->leftJoin('role as r', 'r.role_id', '=', 'ur.role_id')
            ->where('caa.conference_id', $conferenceId)
            ->select(
                'ca.admin_id',
                'ca.name',
                'ca.email',
                'caa.assigned_date',
                'r.role_name',
                'r.role_id'
            )
            ->get();
    }

    /**
     * Get a single administrator's detail within a conference.
     */
    public function getAdminDetail(int $conferenceId, int $adminId): ?object
    {
        return DB::table('conference_admin_assignment as caa')
            ->join('conference_admin as ca', 'ca.admin_id', '=', 'caa.admin_id')
            ->leftJoin('user_role as ur', function ($join) {
                $join->on('ur.user_id', '=', 'ca.admin_id')
                    ->where('ur.user_type', '=', 'conference_admin');
            })
            ->leftJoin('role as r', 'r.role_id', '=', 'ur.role_id')
            ->where('caa.conference_id', $conferenceId)
            ->where('caa.admin_id', $adminId)
            ->select(
                'ca.admin_id',
                'ca.name',
                'ca.email',
                'ca.created_at',
                'ca.updated_at',
                'caa.assigned_date',
                'r.role_name',
                'r.role_id'
            )
            ->first();
    }

    /**
     * Check if a user is the organizer (Chairman) of a conference.
     */
    public function isOrganizer(int $conferenceId, ?int $userId, ?string $userEmail = null): bool
    {
        $conference = Conference::find($conferenceId);
        if (!$conference) {
            return false;
        }

        if ($userEmail && $userEmail === $conference->chairman_email) {
            return true;
        }

        if ($userId) {
            $user = ConferenceAdmin::find($userId);
            return $user && $user->email === $conference->chairman_email;
        }

        return false;
    }

    /**
     * Check if a user has the CONFERENCE_MANAGER role.
     */
    public function isManager(?int $userId): bool
    {
        if (!$userId) {
            return false;
        }

        return DB::table('user_role')
            ->where('user_type', 'conference_admin')
            ->where('user_id', $userId)
            ->join('role', 'role.role_id', '=', 'user_role.role_id')
            ->where('role.role_name', 'CONFERENCE_MANAGER')
            ->exists();
    }

    /**
     * Update an admin member's name (requires Manager authorization).
     */
    public function updateAdminName(int $adminId, string $name, int $currentUserId): bool
    {
        if (!$this->isManager($currentUserId)) {
            abort(403, 'Only Conference Manager can update member names.');
        }

        $admin = ConferenceAdmin::findOrFail($adminId);
        return $admin->update(['name' => $name]);
    }

    /**
     * Invite a Conference Manager (Organizer only).
     */
    public function inviteManager(int $conferenceId, string $email, int $currentUserId): void
    {
        if (!$this->isOrganizer($conferenceId, $currentUserId)) {
            abort(403, 'Only the Conference Organizer (Chairman) can invite a Manager.');
        }

        $this->inviteUser($conferenceId, $email, 'CONFERENCE_MANAGER');
    }

    /**
     * Invite a Conference Admin (Manager only).
     */
    public function inviteAdmin(int $conferenceId, string $email): void
    {
        $this->inviteUser($conferenceId, $email, 'CONFERENCE_ADMIN');
    }

    /**
     * Core invite user logic (creates account, assigns to conference, sets role, sends credentials).
     */
    public function inviteUser(int $conferenceId, string $email, string $roleName): void
    {
        // 1. Check if user exists in conference_admin
        $admin = ConferenceAdmin::where('email', $email)->first();
        $password = null;

        if (!$admin) {
            $password = Str::random(10);
            $admin = ConferenceAdmin::create([
                'name' => explode('@', $email)[0],
                'email' => $email,
                'password' => Hash::make($password),
            ]);
        }

        // 2. Assign to Conference
        $exists = ConferenceAdminAssignment::where('admin_id', $admin->admin_id)
            ->where('conference_id', $conferenceId)
            ->exists();

        if (!$exists) {
            ConferenceAdminAssignment::create([
                'assigned_date' => now(),
                'access_level' => 'admin',
                'admin_id' => $admin->admin_id,
                'conference_id' => $conferenceId,
            ]);
        }

        // 3. Assign Role (Manager or Admin)
        $roleId = DB::table('role')->where('role_name', $roleName)->value('role_id');

        $existingRole = DB::table('user_role')
            ->where('user_type', 'conference_admin')
            ->where('user_id', $admin->admin_id)
            ->first();

        if ($existingRole) {
            if ($roleName === 'CONFERENCE_MANAGER' && $existingRole->role_id != $roleId) {
                DB::table('user_role')
                    ->where('user_role_id', $existingRole->user_role_id)
                    ->update(['role_id' => $roleId, 'updated_at' => now()]);
            }
        } else {
            DB::table('user_role')->insert([
                'user_type' => 'conference_admin',
                'user_id' => $admin->admin_id,
                'role_id' => $roleId,
                'assigned_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 4. Send Email
        if ($password) {
            $conferenceName = Conference::where('conference_id', $conferenceId)->value('name');
            try {
                Mail::to($email)->send(new ConferenceApproved(
                    $conferenceName,
                    $email,
                    $password
                ));
            } catch (\Exception $e) {
                // Ignore email failure in dev
            }
        }
    }

    /**
     * Update access level (promoting/demoting manager/admin).
     */
    public function updateAccessLevel(int $conferenceId, int $targetAdminId, string $newLevel, int $currentUserId): string
    {
        if (!$this->isManager($currentUserId)) {
            abort(403, 'Only Conference Manager can update access levels.');
        }

        $managerRoleId = DB::table('role')->where('role_name', 'CONFERENCE_MANAGER')->value('role_id');
        $adminRoleId = DB::table('role')->where('role_name', 'CONFERENCE_ADMIN')->value('role_id');

        if ($newLevel === 'manager') {
            // Demote current manager (caller) to admin
            DB::table('user_role')
                ->where('user_type', 'conference_admin')
                ->where('user_id', $currentUserId)
                ->update(['role_id' => $adminRoleId, 'updated_at' => now()]);

            ConferenceAdminAssignment::where('conference_id', $conferenceId)
                ->where('admin_id', $currentUserId)
                ->update(['access_level' => 'admin']);

            // Promote target admin to manager
            DB::table('user_role')
                ->where('user_type', 'conference_admin')
                ->where('user_id', $targetAdminId)
                ->update(['role_id' => $managerRoleId, 'updated_at' => now()]);

            ConferenceAdminAssignment::where('conference_id', $conferenceId)
                ->where('admin_id', $targetAdminId)
                ->update(['access_level' => 'manager']);

            return 'Access level updated. You are now an Admin.';
        } else {
            // Demote target to admin
            DB::table('user_role')
                ->where('user_type', 'conference_admin')
                ->where('user_id', $targetAdminId)
                ->update(['role_id' => $adminRoleId, 'updated_at' => now()]);

            ConferenceAdminAssignment::where('conference_id', $conferenceId)
                ->where('admin_id', $targetAdminId)
                ->update(['access_level' => 'admin']);

            return 'Access level updated to Admin.';
        }
    }

    /**
     * Remove an admin member from a conference.
     */
    public function removeAdmin(int $conferenceId, int $adminId): bool
    {
        return (bool) ConferenceAdminAssignment::where('conference_id', $conferenceId)
            ->where('admin_id', $adminId)
            ->delete();
    }

    /**
     * Get statistics for the committee dashboard.
     */
    public function getDashboardStats(int $conferenceId): array
    {
        $conference = Conference::findOrFail($conferenceId);

        $totalPapers = Paper::where('conference_id', $conferenceId)->count();

        $abstractCounts = DB::table('paper')
            ->leftJoin('abstract_submission', 'abstract_submission.paper_id', '=', 'paper.paper_id')
            ->where('paper.conference_id', $conferenceId)
            ->selectRaw("
                COALESCE(abstract_submission.abstract_status, 'No Abstract') as status,
                COUNT(*) as total
            ")
            ->groupByRaw("COALESCE(abstract_submission.abstract_status, 'No Abstract')")
            ->orderBy('status')
            ->get();

        $fullpaperCounts = DB::table('paper')
            ->leftJoin('fullpaper_submission', 'fullpaper_submission.paper_id', '=', 'paper.paper_id')
            ->where('paper.conference_id', $conferenceId)
            ->selectRaw("
                COALESCE(fullpaper_submission.fullpaper_status, 'No Full Paper') as status,
                COUNT(*) as total
            ")
            ->groupByRaw("COALESCE(fullpaper_submission.fullpaper_status, 'No Full Paper')")
            ->orderBy('status')
            ->get();

        $cameraReadySubmitted = DB::table('paper')
            ->join('cameraready_submission', 'cameraready_submission.paper_id', '=', 'paper.paper_id')
            ->where('paper.conference_id', $conferenceId)
            ->count();

        $presenterCounts = DB::table('paper')
            ->leftJoin('presenter_assignment', 'presenter_assignment.paper_id', '=', 'paper.paper_id')
            ->where('paper.conference_id', $conferenceId)
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

        $paymentCounts = DB::table('listener_payment')
            ->join('conference_registration', 'conference_registration.registration_id', '=', 'listener_payment.registration_id')
            ->where('conference_registration.conference_id', $conferenceId)
            ->selectRaw("
                COALESCE(listener_payment.payment_status, 'Pending') as status,
                COUNT(*) as total
            ")
            ->groupByRaw("COALESCE(listener_payment.payment_status, 'Pending')")
            ->orderBy('status')
            ->get();

        return [
            'conference' => $conference,
            'totalPapers' => $totalPapers,
            'abstractCounts' => $abstractCounts,
            'fullpaperCounts' => $fullpaperCounts,
            'cameraReadySubmitted' => $cameraReadySubmitted,
            'presenterCounts' => $presenterCounts,
            'paymentCounts' => $paymentCounts,
        ];
    }
}
