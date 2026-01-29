<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchemeTabFormField extends Model
{
    protected $table = 'scheme_tab_form_fields';

    protected $fillable = [
        'scheme_id',
        'db_column',
        'is_mandatory',
        'tab_field_id',
        'level_name',
        'field_name',
        'field_id',
        'field_type',
        'options',
        'is_common',
        'tab_code',
        'field_class',
        'validation_rule',
        'regex',
        'section_id',
        'is_multiple',
        'field_position',
        'is_active',
    ];

    protected $casts = [
        'options' => 'array',
        'is_common' => 'boolean',
        'is_multiple' => 'boolean',
        'is_active' => 'boolean',
    ];
}
