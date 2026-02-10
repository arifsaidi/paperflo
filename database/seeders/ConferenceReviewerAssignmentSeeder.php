<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConferenceReviewerAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('conference_reviewer_assignment')->insert([
            [
                'invite_date' => now(),
                'invite_status' => 'Accepted',
                'reviewer_type' => 'Internal',
                'reviewer_id' => 1,
                'conference_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'invite_date' => now(),
                'invite_status' => 'Accepted',
                'reviewer_type' => 'External',
                'reviewer_id' => 2,
                'conference_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'invite_date' => now(),
                'invite_status' => 'Pending',
                'reviewer_type' => 'Internal',
                'reviewer_id' => 3,
                'conference_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
