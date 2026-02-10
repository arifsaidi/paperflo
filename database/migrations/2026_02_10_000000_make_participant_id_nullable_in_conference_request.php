<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('conference_request', function (Blueprint $table) {
            // Drop foreign key first if necessary, but usually change() handles it if not changing type
            // But we are making it nullable.
            // SQLite doesn't support dropping FKs easily in resizing, but let's try standard Laravel way.
            // If it fails, I might need to drop FK constraint explicitly.

            // Assuming standard MySQL/PostgreSQL
            $table->unsignedBigInteger('participant_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conference_request', function (Blueprint $table) {
            $table->unsignedBigInteger('participant_id')->nullable(false)->change();
        });
    }
};
