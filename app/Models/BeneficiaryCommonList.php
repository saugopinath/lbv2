<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeneficiaryCommonList extends Model
{
    protected $guarded = [];
    // protected $primaryKey = 'beneficiary_id';
    protected $table = 'lb_scheme.beneficiary_common_lists';
    public function sourceable()
    {
        return $this->morphTo();
    }

    public function beneficiaryPersonal()
    {
        return $this->hasOne(BeneficiaryPersonal::class, 'application_id', 'sourceable_id');
    }
}
