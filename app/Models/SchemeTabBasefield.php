<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchemeTabBasefield extends Model
{
    protected $guarded = [];

     protected $casts = [
        'options'     => 'array',
        'is_common'   => 'boolean',
        'is_multiple' => 'boolean',
        'is_active'   => 'boolean',
    ];
}
