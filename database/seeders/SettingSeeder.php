<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('settings')->truncate();

        Setting::create([
            'app' => 'VILLO Accomodation',
            'description' =>  'Aplikasi Reservasi Villa',
            'logo' =>  '/media/bb.jpg',
            'bg_auth' =>  '/media/misc/bgmalam.jpg',
            'banner' =>  '/media/misc/banner.jpg',
            'pemerintah' =>  'Pemerintahan Provinsi Jamaica',
            'dinas' =>  'Dinas Lingkungan Villa',
            'alamat' =>  '',
            'telepon' =>  '',
            'email' =>  '',
        ]);
    }
}
