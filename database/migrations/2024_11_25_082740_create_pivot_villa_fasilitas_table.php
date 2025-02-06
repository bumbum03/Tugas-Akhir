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
        Schema::create('pivot_villa_fasilitas', function (Blueprint $table) {
            $table->unsignedBigInteger('villa_id');
            $table->unsignedBigInteger('villa_fasilitas_id');
            $table->foreign('villa_id')->references('id')->on('villas')->onDelete('cascade');
            $table->foreign('villa_fasilitas_id')->references('id')->on('villa_fasilitas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pivot_villa_fasilitas');
    }
};
