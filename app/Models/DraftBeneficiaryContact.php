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
    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function block()
    {
        return $this->belongsTo(Block::class, 'block_id');
    }
    public function ward()
    {
        return $this->belongsTo(Ward::class, 'ward_id');
    }

}
