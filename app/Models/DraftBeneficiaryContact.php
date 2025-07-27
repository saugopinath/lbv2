<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DraftBeneficiaryContact extends Model
{
    protected $guarded = [
        'id',
    ];
    protected $table = 'lb_scheme.draft_beneficiary_contacts';
}
