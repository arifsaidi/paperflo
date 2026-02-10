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
        Schema::create('listener_payment', function (Blueprint $table) {
            $table->id('listener_payment_id');
            $table->string('name');
            $table->decimal('amount', 10, 2);
            $table->string('payment_status');
            $table->unsignedBigInteger('registration_id');
            $table->timestamps();

            $table->foreign('registration_id')->references('registration_id')->on('conference_registration')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listener_payment');
    }
};
