<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LbMapping extends Model
{
    protected $table = 'jnmp.lb_mapping';

    protected $fillable = [
        'lb_id',
        'jnm_id',
        'aadhar_hash',
        'payment_suspend'
    ];

    public function beneficiary()
    {
        return $this->belongsTo(BeneficiaryPersonal::class, 'lb_id', 'application_id');
    }

    public function jnmp()
    {
        return $this->belongsTo(JnmpData::class, 'jnm_id', 'application_id');
    }
}
