<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $reviewers = DB::table('reviewer')->get();

        foreach ($reviewers as $reviewer) {
            // Check if role exists
            $exists = DB::table('user_role')
                ->where('user_type', 'reviewer')
                ->where('user_id', $reviewer->reviewer_id)
                ->exists();

            if (!$exists) {
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse this data fix
    }
};
