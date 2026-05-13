<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class JnmpData extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $table = 'jnmp.jnmp_data';
    public $timestamps = false;
    protected $guarded = [];

    protected $primaryKey = 'applicationid';
    public $incrementing = false;

    public function aadhaar()
    {
        return $this->belongsTo(BeneficiaryAadhaar::class, 'aadhaar_hash', 'aadhaar_hash');
    }

    public function beneficiary()
    {
        return $this->belongsTo(BeneficiaryPersonalDetail::class, 'lb_application_id', 'application_id');
    }
}
