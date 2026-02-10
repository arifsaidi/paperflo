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
        Schema::table('review_assignment', function (Blueprint $table) {
            $table->unsignedTinyInteger('score')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('review_assignment', function (Blueprint $table) {
            $table->unsignedTinyInteger('score')->nullable(false)->change();
        });
    }

};
