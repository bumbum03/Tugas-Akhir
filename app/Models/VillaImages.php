<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;

class VillaImages extends Model
{
    use Uuid;
    protected $table = 'villa_images';
    protected $fillable =[
        'image',
        'villa_id'
    ];

    public function villa(){
        return $this->belongsTo(Villa::class);
    }
}
