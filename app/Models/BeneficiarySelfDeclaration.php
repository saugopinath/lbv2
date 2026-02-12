<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeneficiarySelfDeclaration extends Model
{
    protected $guarded = [];
    protected $table = 'lb_scheme.beneficiary_self_declarations';

    protected $casts = [
        'other_details' => 'array',
    ];
}