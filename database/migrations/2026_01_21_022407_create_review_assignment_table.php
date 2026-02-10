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
        Schema::create('review_assignment', function (Blueprint $table) {
            $table->id('review_id');
            $table->unsignedTinyInteger('score');
            $table->text('comment')->nullable();
            $table->string('review_status');
            $table->unsignedBigInteger('fullpaper_id');
            $table->unsignedBigInteger('reviewer_id');
            $table->timestamps();

            $table->foreign('fullpaper_id')->references('fullpaper_id')->on('fullpaper_submission')->onDelete('cascade');
            $table->foreign('reviewer_id')->references('reviewer_id')->on('reviewer')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_assignment');
    }
};
