<?php

namespace App\Models;


class BeneficiarySelfDeclaration extends BaseAuditableModel
{
    protected $guarded = [];
    protected $table = 'pension.beneficiary_self_declarations';
    protected $primaryKey = 'application_id';
    public $incrementing = false;
    protected $casts = [
        'other_details' => 'array',
    ];
}