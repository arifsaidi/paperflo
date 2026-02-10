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
        Schema::create('conference_request', function (Blueprint $table) {
            $table->id('request_id');
            $table->string('conference_title');
            $table->string('org_name');
            $table->string('org_type', 100);
            $table->text('org_address');
            $table->string('applicant_name');
            $table->string('applicant_email');
            $table->string('applicant_phone', 30);
            $table->string('status', 30);
            $table->dateTime('submitted_date');
            $table->unsignedBigInteger('participant_id');
            $table->unsignedBigInteger('superadmin_id');
            $table->timestamps();

            $table->foreign('participant_id')->references('participant_id')->on('participant')->onDelete('cascade');
            $table->foreign('superadmin_id')->references('superadmin_id')->on('super_admin')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conference_request');
    }
};
