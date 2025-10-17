<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmoAtrMaster extends Model
{
    protected $table = 'cmo.cmo_atr_masters';
    protected $primaryKey = 'atn_id';
    protected $fillable = [
        'atr_desc',
        'atr_code',
        'can_find_applicant',
        'atn_id',
    ];
    public $timestamps = false;
}
