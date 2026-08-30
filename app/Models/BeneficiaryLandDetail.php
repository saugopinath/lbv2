<?php

namespace App\Models;

class BeneficiaryLandDetail extends BaseAuditableModel
{
    protected $table = 'pension.land_details';

    protected $guarded = [];

    protected $casts = [
        'other_details' => 'array',
    ];
}
