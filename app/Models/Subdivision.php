<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subdivision extends Model
{
     public function municipality()
    {
        return $this->belongsTo(Municipality::class, 'municipality_id');
    }
}
