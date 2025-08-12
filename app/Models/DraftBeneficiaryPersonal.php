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

    public function ben_relationships()
    {
        return $this->hasMany(DraftBeneficiaryRelationship::class, 'application_id', 'application_id');
    }

public function declarations()
    {
        return $this->hasOne(DraftBeneficiaryDeclaration::class, 'application_id', 'application_id');
    }


    public function contacts()
    {
        return $this->hasOne(DraftBeneficiaryContact::class, 'application_id', 'application_id');
    }


    public function bank()
    {
        return $this->hasOne(DraftBeneficiaryBank::class, 'application_id', 'application_id');
    }


    public function aadhaar()
    {
        return $this->hasOne(BeneficiaryAadhaar::class, 'application_id', 'application_id');
    }


    public function subdivision() {
    return $this->belongsTo(Subdivision::class, 'subdivision_id');
}

public function block() {
    return $this->belongsTo(Block::class, 'block_id');
}

}
