<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class DraftBeneficiaryPersonal extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $guarded = [];
    protected $primaryKey = 'application_id';
    protected $table = 'lb_scheme.draft_beneficiary_personals';
    public function relationships()
    {
        return $this->hasMany(DraftBeneficiaryRelationship::class, 'application_id');
    }
    public function documents()
    {
        return $this->hasMany(BeneficiaryEnclosure::class, 'application_id');
    }
    public function contact()
    {
        return $this->hasOne(DraftBeneficiaryContact::class, 'application_id');
    }
    public function bank()
    {
        return $this->hasOne(DraftBeneficiaryBank::class, 'application_id');
    }
    public function declaration()
    {
        return $this->hasOne(DraftBeneficiaryDeclaration::class, 'application_id');
    }
    public function aadhaar()
    {
        return $this->hasOne(BeneficiaryAadhaar::class, 'application_id');
    }
    public function lists()
    {
        return $this->morphOne(BeneficiaryCommonList::class, 'sourceable');
    }
    protected static function booted()
    {
        static::created(function ($draftbenPar) {
            $draftbenPar->lists()->create([
                'beneficiary_id'     => $draftbenPar->beneficiary_id,
                'district_id'     => $draftbenPar->district_id,
                'block_id'        => $draftbenPar->block_id,
                'sub_division_id' => $draftbenPar->sub_division_id,
                'municipality_id' => $draftbenPar->municipality_id,
                'ward_id'         => $draftbenPar->ward_id,
                'panchayat_id'    => $draftbenPar->panchayat_id,
                'encoded_aadhar'    => $draftbenPar->aadhaar->encoded_aadhar,
                'mobile_no' => $draftbenPar->mobile_no,
            ]);
        });
        static::updated(function ($draftbenPar) {
            if ($draftbenPar->lists) {
                $draftbenPar->lists->update([
                    'mobile_no' => $draftbenPar->mobile_no,
                ]);
            }
        });
    }
}
