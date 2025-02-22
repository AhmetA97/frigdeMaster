<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;


    // relation with blocks
    public function blocks()
    {
        return $this->hasMany(Block::class);
    }
}
