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
        Schema::create('paper_author', function (Blueprint $table) {
            $table->id('paper_author_id');
            $table->string('name');
            $table->string('author_name');
            $table->string('author_email');
            $table->boolean('is_corresponding_author');
            $table->unsignedBigInteger('paper_id');
            $table->unsignedBigInteger('participant_id')->nullable();
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
        Schema::dropIfExists('paper_author');
    }
};
