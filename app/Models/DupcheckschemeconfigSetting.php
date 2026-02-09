<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DupcheckschemeconfigSetting extends Model
{
    protected $guarded = ['id'];
    protected $casts = [
        'scheme_lists' => 'array',
    ];
}
