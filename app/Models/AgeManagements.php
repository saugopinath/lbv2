<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgeManagements extends Model
{
    protected $casts = [
        'special_case' => 'array',
    ];
    protected $guarded = [];
}
