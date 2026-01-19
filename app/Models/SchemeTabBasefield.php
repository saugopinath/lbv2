<?php

namespace App\Models;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class SchemeTabBasefield extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $guarded = [];

     protected $casts = [
        'options'     => 'array',
        'is_common'   => 'boolean',
        'is_multiple' => 'boolean',
        'is_active'   => 'boolean',
    ];
}
