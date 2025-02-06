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
        Schema::create('villa_images', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->string('image');
            $table->unsignedBigInteger('villa_id');
            $table->foreign('villa_id')->references('id')->on('villas')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('villa_images');
    }
};
