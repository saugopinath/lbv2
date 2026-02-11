<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeneficiaryBankDetail extends Model
{
    protected $table = "lb_scheme.beneficiary_bank_details";
    protected $guarded = [];
    protected $casts = [
        'other_details' => 'array',
    ];
}
