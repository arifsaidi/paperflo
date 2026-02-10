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
        Schema::create('presenter_assignment', function (Blueprint $table) {
            $table->id('presenter_assignment_id');
            $table->string('presenter_status');
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
        Schema::dropIfExists('presenter_assignment');
    }
};
