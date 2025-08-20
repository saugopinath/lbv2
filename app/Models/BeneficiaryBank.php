<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeneficiaryBank extends Model
{
    protected $guarded = [
        'id',
    ];
    protected $table = 'lb_scheme.beneficiary_banks';

    public function ifscCodeMaster()
    {
        return $this->belongsTo(IfscCodeMaster::class, 'ifsc', 'code');
    }

     public function ifscbranch()
    {
        return $this->belongsTo(IfscCodeMaster::class, 'ifsc', 'code');
    }
}
