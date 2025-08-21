<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeneficiaryContact extends Model
{
    protected $guarded = ['id'];
    // protected $primaryKey = 'application_id';
    protected $table = 'lb_scheme.beneficiary_contacts';
     public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }
    public function municipality()
    {
        return $this->belongsTo(Municipality::class, 'municipality_id', 'id');
    }
    public function block()
    {
        return $this->belongsTo(Block::class, 'block_id', 'id');
    }
    public function ward()
    {
        return $this->belongsTo(Ward::class, 'ward_id', 'id');
    }
    public function panchayat()
    {
        return $this->belongsTo(Panchayat::class, 'panchayat_id', 'id');
    }

}
