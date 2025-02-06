<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Villa;

class VillaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Villa::updateOrCreate([
            'nama' => 'Villa Batu Malang',
            'deskripsi' => 'Villa yang sangat bagus dan nyaman',
            'alamat' => 'Jalan Batu Malang',
            'email' => 'Villabama@gmail.com',
            'nomor' => '087362578123',
            'tahun_dibangun' => '2018-01-19',
            'total_kamar' => '8',
            'kapasitas' => '10',
            'check_in' => '06:00',
            'check_out' => '10:00',
            'price' => '900000',
            'kota_id' => '3579',
        ]);
    }
}
