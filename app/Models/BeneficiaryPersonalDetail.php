<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeneficiaryPersonalDetail extends Model
{
    protected $guarded = [];
    protected $table = 'lb_scheme.beneficiary_personal_details';

    protected $casts = [
        'other_details' => 'array',
    ];

    
}
