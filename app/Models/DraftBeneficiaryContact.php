<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DraftBeneficiaryContact extends Model
{
    protected $fillable = [
        'application_id',
        'district_id',
        'rural_urban_id',
        'police_station',
        'village_town_city',
        'post_office',
        'pincode',
        'house_premise_no',
        'block_id',
        'panchayat_id',
        'municipality_id',
        'ward_id',
        'created_by',
    ];
    protected $table = 'lb_scheme.draft_beneficiary_contacts';
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
