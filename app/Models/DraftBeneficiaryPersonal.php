<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
