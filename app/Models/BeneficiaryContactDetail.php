<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeneficiaryContactDetail extends BaseAuditableModel
{
    protected $table = "pension.beneficiary_contacts";
    protected $primaryKey = 'application_id';
    public $incrementing = false;
    protected $guarded = [];
    protected $casts = [
        'other_details' => 'array',
    ];
}
