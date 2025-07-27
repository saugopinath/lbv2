<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DraftBeneficiaryPersonal extends Model
{
    protected $guarded = [
        'application_id',
    ];
    protected $primaryKey = 'application_id';
 protected $table = 'lb_scheme.draft_beneficiary_personals';

}
