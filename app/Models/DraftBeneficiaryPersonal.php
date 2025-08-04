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
        return $this->hasMany(DraftBeneficiaryRelationship::class,'application_id');
    }
}
