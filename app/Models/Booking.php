<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = ['location_id', 'user_id', 'temperature', 'blocks', 'volume',
        'date_start', 'date_end', 'access_code', 'price', 'status', 'block_ids'];


    // this method is not used in denormalized version, only keeping this for testing purposes
    // relation with block
    public function blocks()
    {
        return $this->belongsToMany(Block::class);
    }
}
