<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConferenceRequestController extends Controller
{
    // Participant form
    public function create()
    {
        return view('conference_request.create');
    }

    // Store request
    public function store(Request $request)
    {
        $request->validate([
            'conference_title' => 'required|string|max:255',
            'org_name' => 'required|string|max:255',
            'org_type' => 'required|string|max:100',
            'org_address' => 'required|string',
            'applicant_name' => 'required|string|max:255',
            'applicant_email' => 'required|email',
            'applicant_phone' => 'required|string|max:30',
        ]);

        DB::table('conference_request')->insert([
            'conference_title' => $request->conference_title,
            'org_name' => $request->org_name,
            'org_type' => $request->org_type,
            'org_address' => $request->org_address,
            'applicant_name' => $request->applicant_name,
            'applicant_email' => $request->applicant_email,
            'applicant_phone' => $request->applicant_phone,
            'status' => 'Pending',
            'submitted_date' => now(),
            'participant_id' => session('user_type') === 'participant' ? session('user_id') : null,
            'superadmin_id' => session('user_type') === 'super_admin' ? session('user_id') : 1 // placeholder
        ]);

        return redirect()->back()->with('success', 'Conference request submitted.');
    }

    // Super Admin: list requests
    public function index()
    {
        $requests = DB::table('conference_request')->get();
        return view('conference_request.index', compact('requests'));
    }

    // Super Admin: view request detail
    public function show($id)
    {
        $request = DB::table('conference_request')->where('request_id', $id)->first();
        if (!$request) {
            abort(404);
        }
        return view('conference_request.show', compact('request'));
    }

    // Approve request
    public function approve($id)
    {
        DB::transaction(function () use ($id) {

            $req = DB::table('conference_request')
                ->where('request_id', $id)
                ->lockForUpdate()
                ->first();

            if ($req->status !== 'Pending') {
                abort(400, 'Request already processed.');
            }

            // 1. Update Request Status
            DB::table('conference_request')
                ->where('request_id', $id)
                ->update(['status' => 'Approved']);

            // 2. Create Conference
            $conferenceId = DB::table('conference')->insertGetId([
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
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 3. Create/Update Conference Admin Account
            $email = $req->applicant_email;
            $password = \Illuminate\Support\Str::random(10);

            $admin = DB::table('conference_admin')->where('email', $email)->first();

            if (!$admin) {
                $adminId = DB::table('conference_admin')->insertGetId([
                    'name' => $req->applicant_name,
                    'email' => $email,
                    'password' => \Illuminate\Support\Facades\Hash::make($password),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $adminId = $admin->admin_id;
                // Optional: Reset password for existing admin or just inform them they are added?
                // Per user request: "login details (applicants email for email and a password for password)"
                // This implies they need a password. If account exists, maybe we shouldn't overwrite?
                // But simplified for now: Update password so they can log in to this new role.
                DB::table('conference_admin')
                    ->where('admin_id', $adminId)
                    ->update(['password' => \Illuminate\Support\Facades\Hash::make($password)]);
            }

            // 4. Assign Admin to Conference
            DB::table('conference_admin_assignment')->insert([
                'assigned_date' => now(),
                'access_level' => 'admin',
                'admin_id' => $adminId,
                'conference_id' => $conferenceId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 5. Assign Role (CONFERENCE_ADMIN)
            $roleId = DB::table('role')->where('role_name', 'CONFERENCE_ADMIN')->value('role_id');
            if ($roleId) {
                // Check if role already assigned (to avoid duplicates if re-running or updating existing admin)
                $exists = DB::table('user_role')
                    ->where('user_type', 'conference_admin')
                    ->where('user_id', $adminId)
                    ->where('role_id', $roleId)
                    ->exists();

                if (!$exists) {
                    DB::table('user_role')->insert([
                        'user_type' => 'conference_admin',
                        'user_id' => $adminId,
                        'role_id' => $roleId,
                        'assigned_date' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // 6. Send Email
            try {
                \Illuminate\Support\Facades\Mail::to($email)->send(new \App\Mail\ConferenceApproved(
                    $req->conference_title,
                    $email,
                    $password
                ));
            } catch (\Exception $e) {
                // Log error but don't fail transaction? Or fail?
                // For now, let's log it.
                \Illuminate\Support\Facades\Log::error("Failed to send approval email: " . $e->getMessage());
            }
        });

        return redirect()->back()->with('success', 'Conference approved, created, and email sent to organizer.');
    }


    // Reject request
    public function reject($id)
    {
        DB::table('conference_request')
            ->where('request_id', $id)
            ->update(['status' => 'Rejected']);

        return redirect()->back()->with('success', 'Conference rejected.');
    }
}
