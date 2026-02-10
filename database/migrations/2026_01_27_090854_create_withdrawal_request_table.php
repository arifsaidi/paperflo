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
        Schema::create('withdrawal_request', function (Blueprint $table) {
            $table->id('withdrawal_request_id');
            $table->string('withdrawal_stage', 30);
            $table->text('reason');
            $table->dateTime('request_date');
            $table->unsignedBigInteger('paper_id');
            $table->unsignedBigInteger('participant_id');
            $table->timestamps();

            $table->foreign('paper_id')->references('paper_id')->on('paper')->onDelete('cascade');
            $table->foreign('participant_id')->references('participant_id')->on('participant')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawal_request');
    }
};
