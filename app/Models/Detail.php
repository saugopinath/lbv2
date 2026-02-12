<?php

namespace App\Models;

use App\Models\BaseAuditableModel;

class Detail extends BaseAuditableModel
{

    protected $table = 'lb_scheme.details';

    protected $guarded = [];

    protected $casts = [
        'other_details' => 'array',
    ];

    //
}
