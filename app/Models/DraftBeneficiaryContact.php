<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DraftBeneficiaryContact extends Model
{
    protected $guarded = [
        'id',
    ];
    protected $table = 'lb_scheme.draft_beneficiary_contacts';


    public function panchayat()
{
    return $this->belongsTo(Panchayat::class, 'panchayat_id');
}

public function municipality()
{
    return $this->belongsTo(Municipality::class, 'municipality_id');
}


}
