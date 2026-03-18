<?php

namespace App\Models;

use Laravel\Scout\Searchable;

use Illuminate\Database\Eloquent\Model;

class BeneficiaryContactDetail extends BaseAuditableModel
{
    use Searchable;
    protected $table = "pension.beneficiary_contacts";
    protected $primaryKey = 'application_id';
    public $incrementing = false;
    protected $guarded = [];
    protected $casts = [
        'other_details' => 'array',
    ];

    public function personal()
    {
        return $this->belongsTo(BeneficiaryPersonalDetail::class, 'application_id', 'application_id');
    }

    public function municipality()
    {
        return $this->belongsTo(Municipality::class, 'blockurban', 'id');
    }

     public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    public function block()
    {
        return $this->belongsTo(Block::class, 'blockurban', 'id');
    }

    public function panchayat()
    {
        return $this->belongsTo(Panchayat::class, 'gpward', 'id');
    }

    public function ward()
    {
        return $this->belongsTo(Ward::class, 'gpward', 'id');
    }

    // public function personal()
    // {
    //     return $this->belongsTo(
    //         BeneficiaryPersonalDetail::class,
    //         'application_id',
    //         'application_id'
    //     );
    // }

    // 🔥 Clean Address Accessor
    public function getFullAddressAttribute(): string
    {
        $parts = [];

        if ($this->district?->name) {
            $parts[] = strtoupper($this->district->name);
        }

        if ($this->rural_urban == 2) {
            if ($this->block?->name) {
                $parts[] = strtoupper($this->block->name);
            }

            if ($this->panchayat?->name) {
                $parts[] = strtoupper($this->panchayat->name);
            }
        } else {
            if ($this->ward?->name) {
                $parts[] = strtoupper($this->ward->name);
            }
        }

        return !empty($parts) ? implode(', ', $parts) : 'N/A';
    }
    public function searchableAs()
    {
        return 'pension_beneficiary_contacts';
    }
    public function toSearchableArray()
    {
        return [
            'scheme_id' => $this->scheme_id,
            'application_id' => $this->application_id,
            'beneficiary_id' => $this->beneficiary_id,
            'state' => $this->state,
            'district_id' => $this->district_id,
            'rural_urban' => $this->rural_urban,
            'blockurban' => $this->blockurban,
            'gpward' => $this->gpward,
            'policestation' => $this->policestation,
            'villtowncity' => $this->villtowncity,
            'housepremiseno' => $this->housepremiseno,
            'postoffice' => $this->postoffice,
            'pincode' => $this->pincode,
            'other_details' => $this->other_details,
            'is_clean' => $this->is_clean,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
