<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaultyBeneficiaryPersonal extends Model
{
    protected $guarded = [];
    protected $primaryKey = 'beneficiary_id';
    protected $table = 'lb_scheme.faulty_beneficiary_personals';
}
