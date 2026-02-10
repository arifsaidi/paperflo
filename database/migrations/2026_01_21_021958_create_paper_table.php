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
        Schema::create('paper', function (Blueprint $table) {
            $table->id('paper_id');
            $table->string('title');
            $table->unsignedBigInteger('conference_id');
            $table->unsignedBigInteger('track_id');
            $table->timestamps();

            $table->foreign('conference_id')->references('conference_id')->on('conference')->onDelete('cascade');
            $table->foreign('track_id')->references('track_id')->on('track')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paper');
    }
};
