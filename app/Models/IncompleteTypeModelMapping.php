<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncompleteTypeModelMapping extends Model
{
    protected $table = 'incomplete_type_model_mappings';

    protected $fillable = [
        'incomplete_type_code',
        'table_column',
        'model_name',
    ];

    public function applicantIncompleteDetails()
    {
        return $this->hasMany(ApplicantIncompleteDetail::class, 'incomplete_type', 'incomplete_type_code');
    }
}
