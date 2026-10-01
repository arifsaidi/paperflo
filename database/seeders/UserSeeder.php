<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // super_admin
        DB::table('super_admin')->insert([
            [
                'superadmin_id' => 1,
                'name' => 'System Administrator',
                'email' => 'admin@cms.test',
                'password' => Hash::make('admin123'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);

        // participant
        DB::table('participant')->insert([
            [
                'participant_id' => 10,
                'name' => 'Amirul Arif',
                'email' => 'amirul@cms.test',
                'password' => Hash::make('user123'),
                'institution' => 'UiTM',
                'faculty' => 'FSKM',
                'department' => 'Information Systems',
                'address' => 'Shah Alam',
                'phone_num' => '0123456789',
                'registration_date' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);

        // conference_admin
        DB::table('conference_admin')->insert([
            [
                'admin_id' => 3,
                'name' => 'Siti Aisyah',
                'email' => 'admin1@icis.test',
                'password' => Hash::make('manager123'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);

        // user_role
        DB::table('user_role')->insert([
            [
                'user_role_id' => 1,
                'user_type' => 'super_admin',
                'user_id' => 1,
                'role_id' => 1,
                'scope_type' => null,
                'scope_id' => null,
                'assigned_date' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_role_id' => 2,
                'user_type' => 'participant',
                'user_id' => 10,
                'role_id' => 5,
                'scope_type' => null,
                'scope_id' => null,
                'assigned_date' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'user_role_id' => 3,
                'user_type' => 'conference_admin',
                'user_id' => 3,
                'role_id' => 3, // CONFERENCE_ADMIN (or 2 for manager)
                'scope_type' => 'conference',
                'scope_id' => 1,
                'assigned_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}
