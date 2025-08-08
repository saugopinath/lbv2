<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DraftBeneficiaryDeclaration extends Model
{
    protected $guarded = [
        'id',
    ];
     protected $table = 'lb_scheme.draft_beneficiary_declarations';
}
