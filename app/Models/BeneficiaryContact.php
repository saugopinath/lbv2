<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class BeneficiaryContact extends Model implements Auditable
{
    protected $guarded = [
        'id',
    ];
    use \OwenIt\Auditing\Auditable;
    // protected $primaryKey = 'application_id';
    protected $table = 'lb_scheme.beneficiary_contacts';

    public $timestamps = false;

    public function block()
    {
        return $this->belongsTo(Block::class, 'block_id');
    }

    public function panchayat()
    {
        return $this->belongsTo(Panchayat::class, 'panchayat_id');
    }

    public function ward()
    {
        return $this->belongsTo(Ward::class, 'ward_id');
    }

    public function father()
    {
        return $this->hasMany(DraftBeneficiaryRelationship::class, 'application_id', 'application_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }
    public function municipality()
    {
        return $this->belongsTo(Municipality::class, 'municipality_id', 'id');
    }
}
