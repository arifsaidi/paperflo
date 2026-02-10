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
        Schema::create('fullpaper_submission', function (Blueprint $table) {
            $table->id('fullpaper_id');
            $table->string('file_path');
            $table->string('status');
            $table->string('fullpaper_status');
            $table->dateTime('submitted_date');
            $table->unsignedBigInteger('paper_id');
            $table->timestamps();

            $table->foreign('paper_id')->references('paper_id')->on('paper')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fullpaper_submission');
    }
};
