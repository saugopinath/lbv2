<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class BeneficiaryAadhaar extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $guarded = [];
    protected $primaryKey = 'application_id';
    protected $table = 'lb_scheme.beneficiary_aadhaars';

    public function jnmp()
    {
        return $this->hasOne(JnmpData::class, 'aadhar_hash', 'aadhar_hash');
    }
}
