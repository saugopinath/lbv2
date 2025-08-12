<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class District extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function blocks()
    {
        return $this->hasMany(Block::class);
    }

    public function municipalities()
    {
        return $this->hasMany(Municipality::class);
    }
    
    /*public function panchayats()
    {
        return $this->hasManyThrough(Panchayat::class, Block::class);
    }*/
}
