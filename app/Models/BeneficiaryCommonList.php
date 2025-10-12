<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use OwenIt\Auditing\Contracts\Auditable;
class BeneficiaryCommonList extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $guarded = [];
    // protected $primaryKey = 'beneficiary_id';
    protected $table = 'lb_scheme.beneficiary_common_lists';
    public function sourceable()
    {
        return $this->morphTo();
    }


    //jodi commonlist model thake ei gula bandha kore dibo
    public function faultyBeneficiaryPersonal()
    {
        return $this->hasOne(FaultyBeneficiaryPersonal::class, 'beneficiary_id', 'sourceable_id');
    }
    public function faultyBeneficiaryBank()
    {
        return $this->hasOne(FaultyBeneficiaryBank::class, 'application_id', 'sourceable_id');
    }
    public function failedPaymentDetails()
    {
        return $this->hasOne(FailedPaymentDetailNew::class, 'ben_id', 'beneficiary_id');
    }
    public function benPaymentDetails()
    {
        return $this->hasOne(BenPaymentDetails::class, 'ben_id', 'beneficiary_id');
    }
    public function aadhaar()
    {
        return $this->hasOne(BeneficiaryAadhaar::class, 'application_id', 'sourceable_id');
    }

    public function bank()
    {
        return $this->hasOne(BeneficiaryBank::class, 'application_id', 'sourceable_id');
    }

    public function enclosures()
    {
        return $this->hasMany(BeneficiaryTemEnclosure::class, 'application_id', 'sourceable_id');
    }

     public function enclosuresUpdated()
    {
        return $this->hasMany(BeneficiaryEnclosure::class, 'application_id', 'sourceable_id');
    }

    // public function beneficiaryPersonal()
    // {
    //     return $this->hasOne(BeneficiaryPersonal::class, 'application_id', 'sourceable_id');
    // }

    public function beneficiaryBank()
    {
        return $this->hasOne(BeneficiaryBank::class, 'application_id', 'sourceable_id');
    }

    public function block()
    {
        return $this->belongsTo(Block::class, 'block_id');
    }

    public function panchayat()
    {
        return $this->belongsTo(Panchayat::class, 'panchayat_id');
    }

    public function ward()
    {
        return $this->belongsTo(Ward::class, 'ward_id');
    }

    public function municipality()
    {
        return $this->belongsTo(Municipality::class, 'municipality_id');
    }

    public function subdivision()
    {
        return $this->belongsTo(Subdivision::class, 'sub_division_id');
    }
}
