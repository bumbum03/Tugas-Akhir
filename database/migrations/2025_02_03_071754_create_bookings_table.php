<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->string('booking_number')->unique();
            $table->enum('payment_type', ['Offline', 'Online'])->default('offline');
            $table->double('total_price');
            $table->string('email');
            $table->string('name');
            $table->string('phone');
            $table->string('payment_status');
            $table->date('booking_date');
            $table->date('end_date');
            $table->unsignedBigInteger('villa_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('status', ['Active', 'Non Active'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
