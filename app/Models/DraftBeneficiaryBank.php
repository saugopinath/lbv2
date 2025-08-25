<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DraftBeneficiaryBank extends Model
{
     protected $guarded = [
        'id',
    ];
    protected $table = 'lb_scheme.draft_beneficiary_banks';

    // public function ifscMaster()
    // {
    //     return $this->belongsTo(Ifsccodemaster::class, 'ifsc', 'code');
    // }

     public function ifscCodeMaster()
    {
        return $this->belongsTo(IfscCodeMaster::class, 'ifsc', 'code');
    }
    public function ifscbranch()
    {
        return $this->belongsTo(IfscCodeMaster::class, 'ifsc', 'code');
    }
}