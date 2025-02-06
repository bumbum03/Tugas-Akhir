<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pivot_villa_fasilitas extends Model
{
    use Uuid;
    protected $table = 'pivot_villa_fasilitas';
    protected $fillable = [
        'villa_id',
        'villa_fasilitas_id',
    ];

    public function villas()
    {
        return $this->belongsToMany(Villa::class, 'pivot_villa_fasilitas');
    }

    public function villaFasilitas()
    {
        return $this->belongsToMany(VillaFasilitas::class, 'pivot_villa_fasilitas');
    }
}
