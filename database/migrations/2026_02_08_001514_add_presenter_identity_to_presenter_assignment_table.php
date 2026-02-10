<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('presenter_assignment', function (Blueprint $table) {
            $table->string('presenter_name')->nullable()->after('participant_id');
            $table->string('presenter_email')->nullable()->after('presenter_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('presenter_assignment', function (Blueprint $table) {
            $table->dropColumn(['presenter_name', 'presenter_email']);
        });
    }
};
