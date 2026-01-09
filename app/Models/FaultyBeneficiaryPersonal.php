<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaultyBeneficiaryPersonal extends Model
{
    protected $guarded = [];
    protected $primaryKey = 'application_id';
    protected $table = 'lb_scheme.faulty_beneficiary_personals';
    public function lists()
    {
        return $this->morphOne(BeneficiaryCommonList::class, 'sourceable');
    }
    // public function contact()
    // {
    //     return $this->hasOne(FaultyBeneficiaryContact::class, 'application_id');
    // }

    public function bank()
    {
        return $this->hasOne(FaultyBeneficiaryBank::class, 'beneficiary_id', 'beneficiary_id');
    }
}
