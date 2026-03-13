<?php

namespace App\Models;

use App\Models\BaseAuditableModel;

class Pan extends BaseAuditableModel
{
    protected $guarded = [];

    protected $table = 'pension.pan';

    protected $casts = [
        'other_details' => 'array',
    ];
}
