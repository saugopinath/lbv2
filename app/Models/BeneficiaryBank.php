<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
class BeneficiaryBank extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $guarded = [];
    // protected $primaryKey = 'beneficiary_id';
    protected $table = 'lb_scheme.beneficiary_banks';

    // public function beneficiaryPersonal()
    // {
    //     return $this->belongsTo(BeneficiaryPersonal::class, 'beneficiary_id', 'beneficiary_id');
    // }

    // public function beneficiaryFaultyPersonal()
    // {
    //     return $this->belongsTo(FaultyBeneficiaryPersonal::class, 'beneficiary_id', 'beneficiary_id');
    // }
    public function ifscMaster()
    {
        return $this->belongsTo(Ifsccodemaster::class, 'ifsc', 'code');
    }
}
