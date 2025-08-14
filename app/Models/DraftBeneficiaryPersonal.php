<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DraftBeneficiaryRelationship;

class DraftBeneficiaryPersonal extends Model
{
    protected $guarded = [
        'application_id',
    ];
    protected $primaryKey = 'application_id';
    protected $table = 'lb_scheme.draft_beneficiary_personals';

    public $timestamps = false;

    public function father()
    {
        return $this->hasMany(DraftBeneficiaryRelationship::class, 'application_id', 'application_id');
    }

    public function contact()
    {
        return $this->hasOne(DraftBeneficiaryContact::class, 'application_id', 'application_id');
    }

    public function bank()
    {
        return $this->hasOne(DraftBeneficiaryBank::class, 'application_id');
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
    public function aadhaar()
    {
        return $this->hasOne(BeneficiaryAadhaar::class, 'application_id');
    }

    public function relationships()
    {
        return $this->hasMany(DraftBeneficiaryRelationship::class, 'application_id');
    }
}
