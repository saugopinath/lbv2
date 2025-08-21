<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeneficiaryApprovedList extends Model
{
    protected $guarded = [];
    // protected $primaryKey = 'beneficiary_id';
    protected $table = 'lb_scheme.beneficiary_approved_lists';
    public function sourceable()
    {
        return $this->morphTo();
    }
}
