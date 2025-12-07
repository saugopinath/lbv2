<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JnmpData extends Model
{
    protected $table = 'jnmp.jnmp_data';
    public $timestamps = false;
    protected $guarded = [];

    protected $primaryKey = 'applicationid';
    public $incrementing = false;

    public function aadhaar()
    {
        return $this->belongsTo(BeneficiaryAadhaar::class, 'aadhar_hash', 'aadhar_hash');
    }

    public function beneficiary()
    {
        return $this->belongsTo(BeneficiaryPersonal::class, 'lb_application_id', 'application_id');
    }
}
