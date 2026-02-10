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
        Schema::create('deadline', function (Blueprint $table) {
            $table->id('deadline_id');
            $table->dateTime('abstract_deadline');
            $table->dateTime('fullpaper_deadline');
            $table->dateTime('cameraready_paper_deadline');
            $table->dateTime('participant_registration_deadline');
            $table->dateTime('review_deadline');
            $table->dateTime('listener_payment_deadline');
            $table->dateTime('presenter_payment_deadline');
            $table->unsignedBigInteger('conference_id');
            $table->timestamps();

            $table->foreign('conference_id')->references('conference_id')->on('conference')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deadline');
    }
};
