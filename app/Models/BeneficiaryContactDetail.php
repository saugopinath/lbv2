<?php

namespace App\Models;

class BeneficiaryContactDetail extends BaseAuditableModel
{
    protected $table = "pension.beneficiary_contacts";
    protected $primaryKey = 'application_id';
    public $incrementing = false;
    protected $guarded = [];

    protected $casts = [
        'other_details' => 'array',
    ];  

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

    public function personal()
    {
        return $this->belongsTo(
            BeneficiaryPersonalDetail::class,
            'application_id',
            'application_id'
        );
    }

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
}

