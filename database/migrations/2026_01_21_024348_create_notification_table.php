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
        Schema::create('notification', function (Blueprint $table) {
            $table->id('notification_id');
            $table->text('content');
            $table->string('link')->nullable();
            $table->string('status', 30); // e.g., unread/read
            $table->unsignedBigInteger('conference_id')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->unsignedBigInteger('reviewer_id')->nullable();
            $table->unsignedBigInteger('participant_id')->nullable();
            $table->timestamps();

            $table->foreign('conference_id')->references('conference_id')->on('conference')->onDelete('set null');
            $table->foreign('admin_id')->references('admin_id')->on('conference_admin')->onDelete('set null');
            $table->foreign('reviewer_id')->references('reviewer_id')->on('reviewer')->onDelete('set null');
            $table->foreign('participant_id')->references('participant_id')->on('participant')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification');
    }
};
