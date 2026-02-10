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
        Schema::create('conference_registration', function (Blueprint $table) {
            $table->id('registration_id');
            $table->dateTime('registration_date');
            $table->string('registration_type');
            $table->string('registration_category');
            $table->string('registration_status');
            $table->unsignedBigInteger('participant_id');
            $table->unsignedBigInteger('conference_id');
            $table->timestamps();

            $table->foreign('participant_id')->references('participant_id')->on('participant')->onDelete('cascade');
            $table->foreign('conference_id')->references('conference_id')->on('conference')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conference_registration');
    }
};
