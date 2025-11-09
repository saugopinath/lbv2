<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncompletTypeModelMapping extends Model
{
    protected $table = 'incomplet_type_model_mappings';

    public function applicantIncompleteDetails()
    {
        return $this->hasMany(ApplicantIncompletDeatil::class, 'incomplet_type', 'incomplet_type_code');
    }
}
