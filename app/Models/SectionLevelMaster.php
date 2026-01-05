<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SectionLevelMaster extends Model
{
    protected $table = 'section_level_masters';

    protected $fillable = [
        'section_level_name',
        'section_level_short_name',
        'section_level_code',
        'is_active',
    ];
}
