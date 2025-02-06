<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Villa extends Model
{
    use Uuid;
    
    protected $fillable = [
        'nama',
        'deskripsi',
        'alamat',
        'email',
        'nomor',
        'tahun_dibangun',
        'total_kamar',
        'kapasitas',
        'check_in',
        'check_out',
        'price',
        'kota_id',
        'fasilitas_id',
    ];

    public function villaFasilitas()
    {
        return $this->belongsToMany(VillaFasilitas::class, 'pivot_villa_fasilitas');
    }

    public function getVillaFasilitasIdAttribute(){
        return $this->villaFasilitas()->pluck('villa_fasilitas_id');
    }

    public function villaImage(){
        return $this->hasMany(VillaImages::class);
    }

    public function booking(){
        return $this->hasMany(Booking::class);
    }
}
