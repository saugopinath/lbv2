<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ifsccodemaster extends Model
{

    protected $table = 'public.ifsccodemasters';

    public function bankMaster()
    {
        return $this->belongsTo(Bankmaster::class, 'bankmaster_id');
    }
}
