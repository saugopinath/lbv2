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
    // protected $primaryKey = 'application_id';

    // protected $guarded = [];

    // public $timestamps = false;


    public function father()
    {
        return $this->hasMany(BeneficiaryRelationship::class, 'beneficiary_id', 'beneficiary_id');
    }

    // public function contact()
    // {
    //     return $this->hasOne(BeneficiaryContact::class, 'beneficiary_id', 'beneficiary_id');
    // }

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
        return $this->hasOne(BeneficiaryBank::class, 'beneficiary_id', 'beneficiary_id');
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

    // public function relationships()
    // {
    //     return $this->hasMany(BeneficiaryRelationship::class, 'application_id');
    // }

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
    //  protected static function booted()
    // {
    //     static::created(function ($beneficiary) {
    //         // When a new BeneficiaryPersonal is created, automatically create a related list
    //         $beneficiary->lists()->create([
    //             'district_id'     => $beneficiary->district_id,
    //             'block_id'        => $beneficiary->block_id,
    //             'sub_division_id' => $beneficiary->sub_division_id,
    //             'municipality_id' => $beneficiary->municipality_id,
    //             'ward_id'         => $beneficiary->ward_id,
    //             'panchayat_id'    => $beneficiary->panchayat_id,
    //         ]);
    //     });
    // }
    protected static function booted()
    {
        static::created(function ($beneficiary) {

            $beneficiary->lists()->create([
                'beneficiary_id' => $beneficiary->beneficiary_id,
                'mobile_no'      => $beneficiary->mobile_no,
                // 'encoded_aadhar' => $beneficiary->aadhar ? $beneficiary->aadhar->encoded_aadhar : null,
                'encoded_aadhar' => $beneficiary->aadhaar()->exists() ? $beneficiary->aadhaar->encoded_aadhar : null,

                // 'bank_account_number' => $beneficiary->bank ? $beneficiary->bank->account_number : null,
                'bank_account_number' => $beneficiary->bank()->exists() ? $beneficiary->bank->account_number : null,
                'district_id'     => $beneficiary->district_id,
                'block_id'        => $beneficiary->block_id,
                'sub_division_id' => $beneficiary->sub_division_id,
                'municipality_id' => $beneficiary->municipality_id,
                'ward_id'         => $beneficiary->ward_id,
                'panchayat_id'    => $beneficiary->panchayat_id,
            ]);
        });
    }
    // protected static function booted()
    // {
    //     static::created(function ($beneficiary) {
    //         if ($beneficiary) {
    //             $beneficiary->lists()->update([]);
    //         }
    //     });
    // }
}
