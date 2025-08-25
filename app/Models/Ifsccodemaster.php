<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ifsccodemaster extends Model
{
    public function bankmaster()
    {
        return $this->belongsTo(Bankmaster::class, 'bankmaster_id');
    }
}
