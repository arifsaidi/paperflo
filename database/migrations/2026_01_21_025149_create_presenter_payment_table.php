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
        Schema::create('presenter_payment', function (Blueprint $table) {
            $table->id('presenter_payment_id');
            $table->decimal('amount', 10, 2);
            $table->string('status');
            $table->dateTime('paid_date')->nullable();
            $table->unsignedBigInteger('presenter_assignment_id');
            $table->timestamps();

            $table->foreign('presenter_assignment_id')->references('presenter_assignment_id')->on('presenter_assignment')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presenter_payment');
    }
};
