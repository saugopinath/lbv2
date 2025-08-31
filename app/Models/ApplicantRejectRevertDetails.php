<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicantRejectRevertDetails extends Model
{
    protected $guarded = [
        'id',
    ];
    protected $table = 'lb_scheme.applicant_reject_revert_details';
}
