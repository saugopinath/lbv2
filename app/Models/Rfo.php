<?php

namespace App\Models;

use App\Models\BaseAuditableModel;

class Rfo extends BaseAuditableModel
{
protected $casts = [
    'other_details' => 'array',
];

protected $guarded = [];
protected $table = 'pension.rfo';


    //
}
