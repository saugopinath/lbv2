<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class FaultyBeneficiaryBank extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table = 'lb_scheme.faulty_beneficiary_banks';

    public function ifscMaster()
    {
        return $this->belongsTo(Ifsccodemaster::class, 'ifsc', 'code');
    }
}
