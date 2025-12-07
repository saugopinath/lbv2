<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class BeneficiaryPersonal extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $guarded = [];
    protected $primaryKey = 'application_id';
    protected $table = 'lb_scheme.beneficiary_personals';


    public function father()
    {
        return $this->hasMany(BeneficiaryRelationship::class, 'beneficiary_id', 'beneficiary_id');
    }

    public function jnmp()
    {
        return $this->hasOne(JnmpData::class, 'lb_application_id', 'application_id');
    }

    public function contacts()
    {
        return $this->hasOne(BeneficiaryContact::class, 'application_id', 'application_id');
    }

    public function relationships()
    {
        return $this->hasOne(BeneficiaryRelationship::class, 'application_id', 'application_id');
    }

    public function contact()
    {
        return $this->hasOne(BeneficiaryContact::class, 'application_id', 'application_id');
    }

    public function bank()
    {
        return $this->hasOne(BeneficiaryBank::class, 'application_id', 'application_id');
    }

    public function faultyBeneficiaryPersonal()
    {
        return $this->hasOne(FaultyBeneficiaryPersonal::class, 'application_id', 'application_id');
    }
    public function faultyBeneficiaryBank()
    {
        return $this->hasOne(FaultyBeneficiaryBank::class, 'application_id', 'application_id');
    }

    public function failedPaymentDetails()
    {
        return $this->hasOne(FailedPaymentDetails::class, 'ben_id', 'beneficiary_id');
    }
    public function benPaymentDetails()
    {
        return $this->hasOne(BenPaymentDetails::class, 'ben_id', 'beneficiary_id');
    }

    public function casteName()
    {
        return $this->belongsTo(CodeMaster::class, 'caste', 'id');
    }

    public function enclosers()
    {
        return $this->hasMany(BeneficiaryEnclosure::class, 'application_id', 'application_id');
    }

    public function documents()
    {
        return $this->hasMany(BeneficiaryEnclosure::class, 'application_id');
    }

    public function aadhaar()
    {
        return $this->hasOne(BeneficiaryAadhaar::class, 'application_id');
    }


    public function lists()
    {
        return $this->morphOne(BeneficiaryCommonList::class, 'sourceable');
    }
    // public function bank()
    // {
    //     return $this->hasOne(BeneficiaryBank::class, 'beneficiary_id', 'beneficiary_id');
    // }
    // public function contact()
    // {
    //     return $this->hasOne(BeneficiaryContact::class, 'application_id');
    // }
    // public function relationships()
    // {
    //     return $this->hasMany(BeneficiaryRelationship::class, 'application_id');
    // }

    public function getStatusText()
    {
        return 'Approved';
    }

    protected static function booted()
    {
        static::created(function ($beneficiary) {
            $commonList = BeneficiaryCommonList::find($beneficiary->application_id);
            // dd( get_class($beneficiary));
            if ($commonList) {
                $commonList->update([
                    'sourceable_type' => get_class($beneficiary),
                    'next_level_role_id' => $beneficiary->next_level_role_id,
                ]);
            }
        });
    }
}
