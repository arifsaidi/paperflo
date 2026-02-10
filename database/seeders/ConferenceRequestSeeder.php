<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ConferenceRequestSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('conference_request')->insert([
            [
                'request_id' => 1,
                'conference_title' => 'International Conference on Information Security 2026',
                'org_name' => 'Universiti Teknologi MARA',
                'org_type' => 'University',
                'org_address' => 'Shah Alam, Selangor',
                'applicant_name' => 'Amirul Arif',
                'applicant_email' => 'amirul@cms.test',
                'applicant_phone' => '0123456789',
                'status' => 'Pending',
                'submitted_date' => Carbon::now(),
                'participant_id' => 10,
                'superadmin_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);

        // Optional: notification dummy
        DB::table('notification')->insert([
            [
                'notification_id' => 1,
                'content' => 'Your conference request is pending approval.',
                'link' => '/conference/request/1',
                'status' => 'unread',
                'conference_id' => 1,
                'admin_id' => null,
                'reviewer_id' => null,
                'participant_id' => 10,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
    }
}
