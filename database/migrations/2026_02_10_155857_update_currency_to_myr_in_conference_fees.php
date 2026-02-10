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
        // Update existing records
        DB::table('conference_fees')->update(['currency' => 'MYR']);

        Schema::table('conference_fees', function (Blueprint $table) {
            $table->string('currency')->default('MYR')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert existing records (optional, assuming they were USD before)
        DB::table('conference_fees')->update(['currency' => 'USD']);

        Schema::table('conference_fees', function (Blueprint $table) {
            $table->string('currency')->default('USD')->change();
        });
    }
};
