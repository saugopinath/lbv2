<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeneficiaryPersonal extends Model
{
    protected $table = 'lb_scheme.beneficiary_personals';
    protected $primaryKey = 'beneficiary_id';

    protected $guarded = ['beneficiary_id'];

    public $timestamps = false;


    public function father()
    {
        return $this->hasMany(BeneficiaryRelationship::class, 'beneficiary_id', 'beneficiary_id');
    }

    public function contact()
    {
        return $this->hasOne(BeneficiaryContact::class, 'beneficiary_id', 'beneficiary_id');
    }

     public function contacts()
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
        return $this->hasOne(BeneficiaryAadhaar::class, 'beneficiary_id');
    }

    public function relationships()
    {
        return $this->hasMany(DraftBeneficiaryRelationship::class, 'application_id');
    }

    public function lists()
    {
        return $this->morphOne(BeneficiaryApprovedList::class, 'sourceable');
    }
}
