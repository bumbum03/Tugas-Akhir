<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VillaFasilitas extends Model
{
    use Uuid;
    protected $table = 'villa_fasilitas';
    protected $fillable = [
        'nama',
    ];

    public function villa()
    {
        return $this->belongsToMany(Villa::class, 'pivot_villa_fasilitas');
    }
}
