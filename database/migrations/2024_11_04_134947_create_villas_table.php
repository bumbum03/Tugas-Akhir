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
        Schema::create('villas', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->string('nama');
            $table->string('deskripsi');
            $table->string('alamat');
            $table->string('email');
            $table->string('nomor');
            $table->date('tahun_dibangun');
            $table->string('total_kamar');
            $table->string('kapasitas');
            $table->time('check_in');
            $table->time('check_out');
            $table->double('price');
            $table->string('kota_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('villas');
    }
};
