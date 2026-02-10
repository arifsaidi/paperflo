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
        Schema::create('conference_admin_assignment', function (Blueprint $table) {
            $table->id('assignment_id');
            $table->dateTime('assigned_date');
            $table->string('access_level', 50);
            $table->unsignedBigInteger('admin_id');
            $table->unsignedBigInteger('conference_id');
            $table->timestamps();

            $table->foreign('admin_id')->references('admin_id')->on('conference_admin')->onDelete('cascade');
            $table->foreign('conference_id')->references('conference_id')->on('conference')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conference_admin_assignment');
    }
};
