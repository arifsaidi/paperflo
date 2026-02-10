<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('conference', function (Blueprint $table) {
            $table->string('chairman_name')->nullable()->change();
            $table->string('chairman_email')->nullable()->change();
            $table->string('conference_venue')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('conference', function (Blueprint $table) {
            $table->string('chairman_name')->nullable(false)->change();
            $table->string('chairman_email')->nullable(false)->change();
            $table->string('conference_venue')->nullable(false)->change();
        });
    }
};
