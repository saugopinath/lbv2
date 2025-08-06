<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DraftBeneficiaryRelationship extends Model
{
    protected $guarded = [];
    protected $table = 'lb_scheme.draft_beneficiary_relationships';
    public function personal()
    {
        return $this->belongsTo(DraftBeneficiaryPersonal::class, 'application_id');
    }
}
