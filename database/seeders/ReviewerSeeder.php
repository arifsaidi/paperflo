<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ReviewerSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('reviewer')->insert([
            [
                'name' => 'Dr. Ahmad Rahman',
                'title' => 'Dr.',
                'email' => 'ahmad.reviewer@test.com',
                'password' => Hash::make('password'),
                'area' => 'Software Engineering',
                'institution' => 'Universiti Malaya',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Assoc. Prof. Siti Aminah',
                'title' => 'Assoc. Prof.',
                'email' => 'siti.reviewer@test.com',
                'password' => Hash::make('password'),
                'area' => 'Information Systems',
                'institution' => 'Universiti Teknologi Malaysia',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Dr. Lim Wei Hong',
                'title' => 'Dr.',
                'email' => 'lim.reviewer@test.com',
                'password' => Hash::make('password'),
                'area' => 'Artificial Intelligence',
                'institution' => 'Monash University Malaysia',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // 🔐 Assign REVIEWER role (role_id = 4) to each reviewer in user_role table
        $reviewers = DB::table('reviewer')->get();

        foreach ($reviewers as $reviewer) {
            DB::table('user_role')->insert([
                'user_type' => 'reviewer',
                'user_id' => $reviewer->reviewer_id,
                'role_id' => 4, // REVIEWER role
                'scope_type' => null,
                'scope_id' => null,
                'assigned_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
