<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeneficiaryPersonal extends Model
{
    protected $guarded = [];
    protected $primaryKey = 'application_id';
    protected $table = 'lb_scheme.beneficiary_personals';

    public function lists()
    {
        return $this->morphOne(BeneficiaryApprovedList::class, 'sourceable');
    }
     public function bank()
    {
        return $this->hasOne(BeneficiaryBank::class, 'beneficiary_id', 'beneficiary_id');
    }
     public function contact()
    {
        return $this->hasOne(BeneficiaryContact::class, 'application_id');
    }

}