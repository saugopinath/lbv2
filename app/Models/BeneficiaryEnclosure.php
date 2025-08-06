<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeneficiaryEnclosure extends Model
{
    protected $table = 'lb_scheme.beneficiary_enclosures';
    protected $guarded = [];
    public function personal()
    {
        return $this->belongsTo(DraftBeneficiaryPersonal::class, 'application_id');
    }
}
