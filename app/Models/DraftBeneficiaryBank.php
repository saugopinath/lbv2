<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DraftBeneficiaryBank extends Model
{
     protected $guarded = [
          'id',
     ];
     protected $table = 'lb_scheme.draft_beneficiary_banks';
     public function ifscbranch()
     {
          return $this->belongsTo(IfscCodeMaster::class, 'ifsc', 'code');
     }
}
