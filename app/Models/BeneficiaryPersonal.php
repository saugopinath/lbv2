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
        return $this->morphOne(BeneficiaryCommonList::class, 'sourceable');
    }
     public function bank()
    {
        return $this->hasOne(BeneficiaryBank::class, 'beneficiary_id', 'beneficiary_id');
    }
     public function contact()
    {
        return $this->hasOne(BeneficiaryContact::class, 'application_id');
    }
     protected static function booted()
    {
        static::created(function ($beneficiary) {
            // When a new BeneficiaryPersonal is created, automatically create a related list
            $beneficiary->lists()->create([
                'district_id'     => $beneficiary->district_id,
                'block_id'        => $beneficiary->block_id,
                'sub_division_id' => $beneficiary->sub_division_id,
                'municipality_id' => $beneficiary->municipality_id,
                'ward_id'         => $beneficiary->ward_id,
                'panchayat_id'    => $beneficiary->panchayat_id,
            ]);
        });
    }

}
