<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeneficiaryContactDetail extends Model
{
    protected $table = "lb_scheme.beneficiary_contact_details";
    protected $guarded = [];
    protected $casts = [
        'other_details' => 'array',
    ];
    public function municipality()
    {
        return $this->belongsTo(Municipality::class, 'municipality_id');
    }

    public function panchayat()
    {
        return $this->belongsTo(Panchayat::class, 'panchayat_id');
    }
}
