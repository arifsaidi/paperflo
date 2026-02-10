<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ConferenceSeeder extends Seeder
{
    public function run(): void
    {
        // conference
        DB::table('conference')->insert([
            [
                'conference_id' => 1,
                'name' => 'ICIS 2026',
                'logo' => 'icis_logo.png',
                'chairman_name' => 'Dr Ahmad Rahman',
                'chairman_email' => 'ahmad@uitm.edu.my',
                'conference_phone' => '031234567',
                'conference_email' => 'icis@uitm.edu.my',
                'conference_website' => 'https://icis2026.my',
                'secretariat_address' => 'Shah Alam, Selangor',
                'conference_date' => '2026-08-10',
                'publication' => 'IEEE',
                'conference_venue' => 'Dewan Seri Budiman',
                'contents' => 'Conference on Information Security',
                'conference_status' => 'draft',
                'organizer_participant_id' => 10,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);

        // conference_admin_assignment
        DB::table('conference_admin_assignment')->insert([
            [
                'assignment_id' => 1,
                'assigned_date' => Carbon::now(),
                'access_level' => 'MANAGER',
                'admin_id' => 3,
                'conference_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);

        // deadline
        DB::table('deadline')->insert([
            [
                'deadline_id' => 1,
                'abstract_deadline' => '2026-03-01 23:59:59',
                'fullpaper_deadline' => '2026-04-01 23:59:59',
                'cameraready_paper_deadline' => '2026-05-01 23:59:59',
                'participant_registration_deadline' => '2026-06-01 23:59:59',
                'review_deadline' => '2026-04-15 23:59:59',
                'listener_payment_deadline' => '2026-06-05 23:59:59',
                'presenter_payment_deadline' => '2026-05-10 23:59:59',
                'conference_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
    }
}
