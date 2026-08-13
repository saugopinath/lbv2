<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncompleteTypeModelMapping extends Model
{
    protected $table = 'incomplete_type_model_mappings';

    public function applicantIncompleteDetails()
    {
        return $this->hasMany(ApplicantIncompleteDetail::class, 'incomplete_type', 'incomplete_type_code');
    }
}
