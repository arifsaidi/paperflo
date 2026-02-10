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
        Schema::create('conference', function (Blueprint $table) {
            $table->id('conference_id');
            $table->string('name');
            $table->string('logo')->nullable();
            $table->string('chairman_name');
            $table->string('chairman_email');
            $table->string('conference_phone');
            $table->string('conference_email');
            $table->string('conference_website')->nullable();
            $table->text('secretariat_address');
            $table->dateTime('conference_date');
            $table->string('publication')->nullable();
            $table->string('conference_venue');
            $table->text('contents')->nullable();
            $table->unsignedBigInteger('organizer_participant_id');
            $table->timestamps();

            $table->foreign('organizer_participant_id')->references('participant_id')->on('participant')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conference');
    }
};
