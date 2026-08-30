<?php

namespace App\Models;

class BeneficiaryPersonalIdentification extends BaseAuditableModel
{
    protected $table = 'pension.personal_identification_number(_s)';

    protected $guarded = [];

    protected $casts = [
        'other_details' => 'array',
    ];
}
