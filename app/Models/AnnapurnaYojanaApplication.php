<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnnapurnaYojanaApplication extends Model
{
    protected $connection = 'pgsql_apy_uat';
    protected $table = 'annapurna_yojana_applications';

    protected $guarded = [];

    protected $casts = [
        'form_data' => 'array',
    ];
}
