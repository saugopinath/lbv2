<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmoAtrMaster extends Model
{
    protected $table = 'cmo.cmo_atr_masters';
    protected $fillable = [
        'atr_desc',
        'atr_code',
        'can_find_applicant',
    ];
}
