<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficeMaster extends Model
{
    protected $fillable = [
        'name',
        'address',
        'zip',
        'office_type',
        'state_id',
        'district_id',
        'block_id',
        'subdivision_id',
        'state_id',
        'municipality_id',
        'ward_id'
    ];
    public function officeType()
    {
        return $this->belongsTo(Codemaster::class, 'office_type', 'code');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }
    public function block()
    {
        return $this->belongsTo(Block::class, 'block_id', 'id');
    }
    public function subdivision()
    {
        return $this->belongsTo(Subdivision::class, 'subdivision_id', 'id');
    }
    public function municipality()
    {
        return $this->belongsTo(Municipality::class, 'municipality_id', 'id');
    }
    public function gp()
    {
        return $this->belongsTo(Panchayat::class, 'panchayat_id', 'id');
    }
    public function ward()
    {
        return $this->belongsTo(Ward::class, 'ward_id', 'id');
    }

}
