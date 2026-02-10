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
        Schema::create('conference_reviewer_assignment', function (Blueprint $table) {
            $table->id('conf_reviewer_id');
            $table->dateTime('invite_date');
            $table->string('invite_status');
            $table->string('reviewer_type');
            $table->unsignedBigInteger('reviewer_id');
            $table->unsignedBigInteger('conference_id');
            $table->timestamps();

            $table->foreign('reviewer_id')->references('reviewer_id')->on('reviewer')->onDelete('cascade');
            $table->foreign('conference_id')->references('conference_id')->on('conference')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conference_reviewer_assignment');
    }
};
