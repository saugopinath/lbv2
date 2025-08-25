<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaultyBeneficiaryBank extends Model
{
    protected $table = 'lb_scheme.faulty_beneficiary_banks';

     public function ifscMaster()
    {
        return $this->belongsTo(Ifsccodemaster::class, 'ifsc', 'code');
    }
}
