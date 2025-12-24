<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TabFormField extends Model
{
    protected $table = 'tab_form_fields';
    protected $fillable = [
        'is_common', 'tab_code', 'scheme_id', 'level_name', 'field_name', 'field_id',
        'field_type', 'options', 'validation_rule', 'regex', 'is_active', 'field_position'
    ];
}
