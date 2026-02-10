<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\ConferenceApproved; // Reuse this or create new? Reuse for credential sending.

class CommitteeAdminController extends Controller
{
    public function index($conference_id)
    {
        $conference = DB::table('conference')->where('conference_id', $conference_id)->first();

        // Get all admins assigned to this conference
        $admins = DB::table('conference_admin_assignment as caa')
            ->join('conference_admin as ca', 'ca.admin_id', '=', 'caa.admin_id')
            ->leftJoin('user_role as ur', function ($join) {
                $join->on('ur.user_id', '=', 'ca.admin_id')
                    ->where('ur.user_type', '=', 'conference_admin');
            })
            ->leftJoin('role as r', 'r.role_id', '=', 'ur.role_id')
            ->where('caa.conference_id', $conference_id)
            ->select(
                'ca.admin_id',
                'ca.name',
                'ca.email',
                'caa.assigned_date',
                'r.role_name',
                'r.role_id'
            )
            ->get();

        // Identify if current user is the Organizer (Chairman)
        // Check if current user email matches chairman_email
        $currentUserEmail = session('user_email'); // Assuming we store email in session or retrieve from Auth
        // Or fetch current user
        $currentUserId = session('user_id');
        $currentUser = DB::table('conference_admin')->where('admin_id', $currentUserId)->first();

        $isOrganizer = false;
        if ($currentUser && $currentUser->email === $conference->chairman_email) {
            $isOrganizer = true;
        }

        // Check if I am a Manager (Role ID 2)
        $isManager = false;
        $myRole = DB::table('user_role')
            ->where('user_type', 'conference_admin')
            ->where('user_id', $currentUserId)
            ->join('role', 'role.role_id', '=', 'user_role.role_id')
            ->first();

        if ($myRole && $myRole->role_name === 'CONFERENCE_MANAGER') {
            $isManager = true;
        }

        return view('committee.admins.index', compact('conference', 'admins', 'isOrganizer', 'isManager'));
    }

    public function show($conference_id, $admin_id)
    {
        $conference = DB::table('conference')->where('conference_id', $conference_id)->first();

        if (!$conference) {
            abort(404);
        }

        $admin = DB::table('conference_admin_assignment as caa')
            ->join('conference_admin as ca', 'ca.admin_id', '=', 'caa.admin_id')
            ->leftJoin('user_role as ur', function ($join) {
                $join->on('ur.user_id', '=', 'ca.admin_id')
                    ->where('ur.user_type', '=', 'conference_admin');
            })
            ->leftJoin('role as r', 'r.role_id', '=', 'ur.role_id')
            ->where('caa.conference_id', $conference_id)
            ->where('caa.admin_id', $admin_id)
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

        if (!$admin) {
            abort(404);
        }

        // Check if current user is Manager
        $currentUserId = session('user_id');
        $myRole = DB::table('user_role')
            ->where('user_type', 'conference_admin')
            ->where('user_id', $currentUserId)
            ->join('role', 'role.role_id', '=', 'user_role.role_id')
            ->first();

        $isManager = $myRole && $myRole->role_name === 'CONFERENCE_MANAGER';

        return view('committee.admins.show', compact('conference', 'admin', 'isManager'));
    }

    public function updateName(Request $request, $conference_id, $admin_id)
    {
        // Only Manager can update names
        $currentUserId = session('user_id');
        $myRole = DB::table('user_role')
            ->where('user_type', 'conference_admin')
            ->where('user_id', $currentUserId)
            ->join('role', 'role.role_id', '=', 'user_role.role_id')
            ->first();

        if (!$myRole || $myRole->role_name !== 'CONFERENCE_MANAGER') {
            abort(403, 'Only Conference Manager can update member names.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        DB::table('conference_admin')
            ->where('admin_id', $admin_id)
            ->update(['name' => $request->name, 'updated_at' => now()]);

        return redirect()->back()->with('success', 'Name updated successfully.');
    }

    // Organizer invites Manager
    public function inviteManager(Request $request, $conference_id)
    {
        // 1. Validate: Only Organizer can do this.
        $conference = DB::table('conference')->where('conference_id', $conference_id)->first();
        $currentUserId = session('user_id');
        $currentUser = DB::table('conference_admin')->where('admin_id', $currentUserId)->first();

        if (!$currentUser || $currentUser->email !== $conference->chairman_email) {
            abort(403, 'Only the Conference Organizer (Chairman) can invite a Manager.');
        }

        $request->validate([
            'email' => 'required|email'
        ]);

        $this->inviteUser($conference_id, $request->email, 'CONFERENCE_MANAGER');

        return redirect()->back()->with('success', 'Conference Manager invited successfully.');
    }

    // Manager invites Admin
    public function store(Request $request, $conference_id)
    {
        // Middleware should handle permission check for MANAGE_COMMITTEE
        // But double check here if needed.

        $request->validate([
            'email' => 'required|email'
        ]);

        $this->inviteUser($conference_id, $request->email, 'CONFERENCE_ADMIN');

        return redirect()->back()->with('success', 'Conference Admin invited successfully.');
    }

    private function inviteUser($conference_id, $email, $roleName)
    {
        // 1. Check if user exists in conference_admin
        $admin = DB::table('conference_admin')->where('email', $email)->first();
        $password = null;

        if (!$admin) {
            // Create new admin
            $password = Str::random(10);
            $adminId = DB::table('conference_admin')->insertGetId([
                'name' => explode('@', $email)[0], // Placeholder name
                'email' => $email,
                'password' => Hash::make($password),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $admin = (object) ['admin_id' => $adminId, 'name' => explode('@', $email)[0]];
        } else {
            // User exists, just link them
        }

        // 2. Assign to Conference
        // Check if already assigned
        $exists = DB::table('conference_admin_assignment')
            ->where('admin_id', $admin->admin_id)
            ->where('conference_id', $conference_id)
            ->exists();

        if (!$exists) {
            DB::table('conference_admin_assignment')->insert([
                'assigned_date' => now(),
                'access_level' => 'admin', // Generic level
                'admin_id' => $admin->admin_id,
                'conference_id' => $conference_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 3. Assign Role (Manager or Admin)
        $roleId = DB::table('role')->where('role_name', $roleName)->value('role_id');

        // Remove existing role for this scope? 
        // Our RBAC is global per user type. If they are Admin in one conf and Manager in another, 
        // our current schema (user_role without scope) doesn't support it well.
        // Assuming user is dedicated to this conference permissions for now.
        // Or we should update existing role if they are promoted.

        // Check if they already have a role
        $existingRole = DB::table('user_role')
            ->where('user_type', 'conference_admin')
            ->where('user_id', $admin->admin_id)
            ->first();

        if ($existingRole) {
            // Update role if they are being promoted to Manager?
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
        // We can reuse App\Mail\ConferenceApproved or create a new Invitation email.
        // For simplicity, let's reuse ConferenceApproved but purely for credentials if password generated.
        // If password not generated (existing user), maybe send a "You have been added" email.

        if ($password) {
            try {
                Mail::to($email)->send(new ConferenceApproved(
                    DB::table('conference')->where('conference_id', $conference_id)->value('name'),
                    $email,
                    $password
                ));
            } catch (\Exception $e) {
                // Log
            }
        }
    }

    public function updateAccessLevel(Request $request, $conference_id, $admin_id)
    {
        // Only a Manager can change access levels
        $currentUserId = session('user_id');
        $myRole = DB::table('user_role')
            ->where('user_type', 'conference_admin')
            ->where('user_id', $currentUserId)
            ->join('role', 'role.role_id', '=', 'user_role.role_id')
            ->first();

        if (!$myRole || $myRole->role_name !== 'CONFERENCE_MANAGER') {
            abort(403, 'Only Conference Manager can update access levels.');
        }

        $request->validate([
            'access_level' => 'required|in:admin,manager',
        ]);

        $newLevel = $request->access_level;
        $managerRoleId = DB::table('role')->where('role_name', 'CONFERENCE_MANAGER')->value('role_id');
        $adminRoleId = DB::table('role')->where('role_name', 'CONFERENCE_ADMIN')->value('role_id');

        if ($newLevel === 'manager') {
            // Demote current manager (me) to admin
            DB::table('user_role')
                ->where('user_type', 'conference_admin')
                ->where('user_id', $currentUserId)
                ->update(['role_id' => $adminRoleId, 'updated_at' => now()]);

            DB::table('conference_admin_assignment')
                ->where('conference_id', $conference_id)
                ->where('admin_id', $currentUserId)
                ->update(['access_level' => 'admin', 'updated_at' => now()]);

            // Promote target admin to manager
            DB::table('user_role')
                ->where('user_type', 'conference_admin')
                ->where('user_id', $admin_id)
                ->update(['role_id' => $managerRoleId, 'updated_at' => now()]);

            DB::table('conference_admin_assignment')
                ->where('conference_id', $conference_id)
                ->where('admin_id', $admin_id)
                ->update(['access_level' => 'manager', 'updated_at' => now()]);

            return redirect()->back()->with('success', 'Access level updated. You are now an Admin.');
        } else {
            // Demote target to admin (only if they are not the current manager)
            DB::table('user_role')
                ->where('user_type', 'conference_admin')
                ->where('user_id', $admin_id)
                ->update(['role_id' => $adminRoleId, 'updated_at' => now()]);

            DB::table('conference_admin_assignment')
                ->where('conference_id', $conference_id)
                ->where('admin_id', $admin_id)
                ->update(['access_level' => 'admin', 'updated_at' => now()]);

            return redirect()->back()->with('success', 'Access level updated to Admin.');
        }
    }

    public function destroy($conference_id, $admin_id)
    {
        // Remove assignment
        DB::table('conference_admin_assignment')
            ->where('conference_id', $conference_id)
            ->where('admin_id', $admin_id)
            ->delete();

        return redirect()->back()->with('success', 'Admin removed from conference.');
    }
}
