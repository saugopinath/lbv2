<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DraftBeneficiaryRelationship;

class DraftBeneficiaryPersonal extends Model
{
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


    public function subdivision()
    {
        return $this->belongsTo(Subdivision::class, 'subdivision_id');
    }

    public function block()
    {
        return $this->belongsTo(Block::class, 'block_id');
    }
    public function lists()
    {
        return $this->morphOne(BeneficiaryCommonList::class, 'sourceable');
    }
    protected static function booted()
    {
        static::created(function ($beneficiary) {
            // When a new DraftBeneficiaryPersonal is created, automatically create a related list
            $beneficiary->lists()->create([
                'district_id' => $beneficiary->district_id,
                'block_id' => $beneficiary->block_id,
                'sub_division_id' => $beneficiary->sub_division_id,
                'municipality_id' => $beneficiary->municipality_id,
                'ward_id' => $beneficiary->ward_id,
                'panchayat_id' => $beneficiary->panchayat_id,
            ]);
        });
    }

}
