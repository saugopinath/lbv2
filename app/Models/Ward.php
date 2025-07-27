<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ward extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function panchayat()
    {
        return $this->belongsTo(Panchayat::class);
    }

    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }
}
