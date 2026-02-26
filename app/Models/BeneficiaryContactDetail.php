<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeneficiaryContactDetail extends BaseAuditableModel
{
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
        return $this->belongsTo(Municipality::class, 'blockurban');
    }

     public function block()
    {
        return $this->belongsTo(Block::class, 'blockurban');
    }

    public function panchayat()
    {
        return $this->belongsTo(Panchayat::class, 'gpward');
    }

    public function ward()
    {
        return $this->belongsTo(Ward::class, 'gpward');
    }   

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }
    
    public function subdivision()
    {
        return $this->belongsTo(Subdivision::class, 'sub_division_id');
    }

    public function getFullAddress(): string
    {
        $district = optional($this->district)->name;
        $subdivision = optional($this->subdivision)->name;
        $block = optional($this->block)->name;
        $panchayat = optional($this->panchayat)->name;
        $municipality = optional($this->municipality)->name;
        $ward = optional($this->ward)->name;

        $parts = [];

        if ($district) {
            $parts[] = "<b>District - </b>" . strtoupper($district);
        }

        // Rural
        if ($this->rural_urban == 2) {
            if ($block) {
                $parts[] = "<b>Block - </b>" . strtoupper($block);
            }
            if ($panchayat) {
                $parts[] = "<b>GP - </b>" . strtoupper($panchayat);
            }
        }
        // Urban
        else {
            if ($subdivision) {
                $parts[] = "<b>Subdivision - </b>" . strtoupper($subdivision);
            }
            if ($municipality) {
                $parts[] = "<b>Municipality - </b>" . strtoupper($municipality);
            }
            if ($ward) {
                $parts[] = "<b>Ward - </b>" . strtoupper($ward);
            }
        }

        // Use <br> for line breaks in HTML
        return !empty($parts) ? implode('<br>', $parts) : 'N/A';
    }

    public function blockmuni(): array
    {
        $block = optional($this->block)->name;
        $panchayat = optional($this->panchayat)->name;
        $municipality = optional($this->municipality)->name;
        $ward = optional($this->ward)->name;
        $blockname = '';
        $gpname = '';
        if ($this->rural_urban == 2) {
            if ($block) {
                $blockname = strtoupper($block);
            }
            if ($panchayat) {
                $gpname = strtoupper($panchayat);
            }
        } else {
            if ($municipality) {
                $blockname = strtoupper($municipality);
            }
            if ($ward) {
                $gpname = strtoupper($ward);
            }
        }
        return [
            'block' => $blockname,
            'gp' => $gpname
        ];
    }
}
