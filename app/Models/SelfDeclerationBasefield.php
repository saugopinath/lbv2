<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SelfDeclerationBasefield extends Model
{
    protected $table = 'self_decleration_basefields';
    protected $guarded = [];
    protected $casts = [
        'options' => 'array',
        'validation_rule' => 'array',
    ];
}
