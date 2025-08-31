<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BenRejectDetails extends Model
{
    protected $guarded = [
        'id',
    ];
    protected $table = 'lb_scheme.ben_reject_details';
    protected $casts = [
        'personal_details' => 'array',
        'contact_details' => 'array',
        'bank_details' => 'array',
        'declaration_details' => 'array',
        'relationship_details' => 'array',
        'aadhar_details' => 'array',
    ];


    public function lists()
    {
        return $this->morphOne(BeneficiaryCommonList::class, 'sourceable');
    }
}
