<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ifsccodemaster extends Model
{
    public function bank()
    {
        return $this->belongsTo(BankMaster::class, 'bankmaster_id');
    }
}
