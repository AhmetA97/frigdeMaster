<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    use HasFactory;


    // this method is not used in denormalized version, only keeping this for testing purposes
    // relation with booking
    public function bookings()
    {
        return $this->belongsToMany(Booking::class);
    }
}
