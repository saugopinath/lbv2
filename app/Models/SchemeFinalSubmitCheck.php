<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchemeFinalSubmitCheck extends Model
{
    protected $fillable = [
        'scheme_id',
        'is_final_submitted',
    ];
}
