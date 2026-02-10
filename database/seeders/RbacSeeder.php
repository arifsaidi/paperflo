<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RbacSeeder extends Seeder
{
    public function run(): void
    {
        // roles
        DB::table('role')->insert([
            ['role_id' => 1, 'role_name' => 'SUPER_ADMIN'],
            ['role_id' => 2, 'role_name' => 'CONFERENCE_MANAGER'],
            ['role_id' => 3, 'role_name' => 'CONFERENCE_ADMIN'],
            ['role_id' => 4, 'role_name' => 'REVIEWER'],
            ['role_id' => 5, 'role_name' => 'PARTICIPANT'],
        ]);

        // permissions
        DB::table('permission')->insert([
            ['permission_id' => 1, 'permission_code' => 'APPROVE_CONFERENCE', 'description' => 'Approve or reject conference requests'],
            ['permission_id' => 2, 'permission_code' => 'MANAGE_COMMITTEE', 'description' => 'Manage conference committee access'],
            ['permission_id' => 3, 'permission_code' => 'MANAGE_CONFERENCE', 'description' => 'Manage conference details'],
            ['permission_id' => 4, 'permission_code' => 'REVIEW_PAPER', 'description' => 'Review assigned papers'],
            ['permission_id' => 5, 'permission_code' => 'SUBMIT_PAPER', 'description' => 'Submit and manage papers'],
            ['permission_id' => 6, 'permission_code' => 'SUBMIT_CONFERENCE_REQUEST', 'description' => 'Submit conference request'],
        ]);

        // role_permission
        DB::table('role_permission')->insert([
            ['role_id' => 1, 'permission_id' => 1],
            ['role_id' => 2, 'permission_id' => 2],
            ['role_id' => 2, 'permission_id' => 3],
            ['role_id' => 3, 'permission_id' => 3],
            ['role_id' => 4, 'permission_id' => 4],
            ['role_id' => 5, 'permission_id' => 5],
            ['role_id' => 5, 'permission_id' => 6], // ✅ participant can submit conference request
        ]);
    }
}
