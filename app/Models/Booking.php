<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use Uuid;
    protected $table = 'bookings';
    protected $guarded = ['id', 'uuid', 'created_at', 'updated_at'];

    public function villa(){
        return $this->belongsTo(Villa::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}


