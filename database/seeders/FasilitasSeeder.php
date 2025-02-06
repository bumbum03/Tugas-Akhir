<?php

namespace Database\Seeders;

use App\Models\VillaFasilitas;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FasilitasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        VillaFasilitas::create([
            'nama' => 'Televisi',
        ]);

        VillaFasilitas::create([
            'nama' => 'Kolam Renang',
        ]);

        VillaFasilitas::create([
            'nama' => 'Gym',
        ]);
    }
}
