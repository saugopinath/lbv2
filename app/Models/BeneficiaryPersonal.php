<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeneficiaryPersonal extends Model
{
    protected $guarded = [];
    protected $primaryKey = 'beneficiary_id';
    protected $table = 'lb_scheme.beneficiary_personals';
}
