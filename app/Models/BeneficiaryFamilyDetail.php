<?php

namespace App\Models;

class BeneficiaryFamilyDetail extends BaseAuditableModel
{
    protected $table = 'pension.beneficiary_family_details';

    protected $guarded = [];

    protected $casts = [
        'other_details' => 'array',
    ];
}
