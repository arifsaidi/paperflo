<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('conference_fees', function (Blueprint $table) {
            $table->id('fee_id');
            $table->unsignedBigInteger('conference_id');
            $table->string('name'); // e.g., Professional, Student, Listener
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('USD');
            $table->timestamps();

            $table->foreign('conference_id')->references('conference_id')->on('conference')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conference_fees');
    }
};
