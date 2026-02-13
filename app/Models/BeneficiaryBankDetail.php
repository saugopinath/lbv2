<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeneficiaryBankDetail extends BaseAuditableModel
{
    protected $table = "pension.beneficiary_banks";
    protected $primaryKey = 'application_id';
    public $incrementing = false;
    protected $guarded = [];
    protected $casts = [
        'other_details' => 'array',
    ];
}
