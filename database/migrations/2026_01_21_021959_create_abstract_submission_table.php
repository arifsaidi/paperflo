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
        Schema::create('abstract_submission', function (Blueprint $table) {
            $table->id('abstract_id');
            $table->text('abstract_text');
            $table->string('status');
            $table->string('abstract_status');
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
        Schema::dropIfExists('abstract_submission');
    }
};
