<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('paper', function (Blueprint $table) {
            $table->text('keywords')->nullable()->after('title');
        });
    }

    public function down(): void
    {
        Schema::table('paper', function (Blueprint $table) {
            $table->dropColumn('keywords');
        });
    }
};
