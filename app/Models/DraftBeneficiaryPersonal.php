<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DraftBeneficiaryRelationship;
use OwenIt\Auditing\Contracts\Auditable;

class DraftBeneficiaryPersonal extends Model implements Auditable
{
    protected $guarded = [
        'application_id',
    ];

    use \OwenIt\Auditing\Auditable;

    protected $primaryKey = 'application_id';
    protected $table = 'lb_scheme.draft_beneficiary_personals';

    public function father()
    {
        return $this->hasMany(DraftBeneficiaryRelationship::class, 'application_id', 'application_id');
    }

    public function casteName()
    {
        return $this->belongsTo(CodeMaster::class, 'caste', 'id');
    }

    // public function enclosers()
    // {
    //     return $this->hasMany(BeneficiaryEnclosure::class, 'application_id', 'application_id');
    // }

    // public function beneficiaryEnclosures()
    // {
    //     return $this->hasMany(BeneficiaryEnclosure::class, 'application_id', 'application_id');
    // }

    public function documents()
    {
        return $this->hasMany(BeneficiaryEnclosure::class, 'application_id');
    }

    public function relationships()
    {
        return $this->hasMany(DraftBeneficiaryRelationship::class, 'application_id');
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
    public function getStatusText(): string
    {
        if ($this->next_level_role_id == Codemaster::getIdByCode(22)) {
            return 'Submitted but Verification Pending';
        } elseif ($this->next_level_role_id == Codemaster::getIdByCode(23)) {
            return 'Verified but Approval Pending';
        } else {
            return 'Partially Submitted';
        }
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
                'encoded_aadhar'    => $draftbenPar->aadhaar->aadhar_hash,
                'mobile_no' => $draftbenPar->mobile_no,
                'beneficiary_name' => $draftbenPar->full_name,
                'next_level_role_id'=> $draftbenPar->next_level_role_id,
            ]);
        });
        static::updated(function ($draftbenPar) {
            if ($draftbenPar->lists) {
                $draftbenPar->lists->update([
                    'mobile_no' => $draftbenPar->mobile_no,
                    'beneficiary_name' => $draftbenPar->full_name,
                    'next_level_role_id'=> $draftbenPar->next_level_role_id,
                ]);
            }
        });
    }
}
