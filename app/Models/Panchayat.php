<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Panchayat extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function block()
    {
        return $this->belongsTo(Block::class);
    }
}
