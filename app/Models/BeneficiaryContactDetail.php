<?php

namespace App\Models;

use Laravel\Scout\Searchable;

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

   

    public function getFullAddress(): string
    {
        $district = optional($this->district)->name;
        $parts = [];

        if ($district) {
            $parts[] = "District - " . strtoupper($district);
        }
        // Rural
        if ($this->rural_urban == 2) {
            $block = optional($this->block)->name;
            $panchayat = optional($this->panchayat)->name;
            if ($block) {
                $parts[] = "Block - " . strtoupper($block);
            }
            if ($panchayat) {
                $parts[] = "GP - " . strtoupper($panchayat);
            }
        }
        // Urban
        else {
            $muni = $this->municipality;
            $municipality = optional($muni)->name;
            $subdivision = $muni ? optional($muni->Subdivision)->name : null;
            $ward = optional($this->ward)->name;

            if ($subdivision) {
                $parts[] = "Subdivision - " . strtoupper($subdivision);
            }
            if ($municipality) {
                $parts[] = "Municipality - " . strtoupper($municipality);
            }
            if ($ward) {
                $parts[] = "Ward - " . strtoupper($ward);
            }
        }

        // Use <br> for line breaks in HTML
        return !empty($parts) ? implode('<br>', $parts) : 'N/A';
    }

    public function blockmuni(): array
    {
        $blockname = '';
        $gpname = '';
        if ($this->rural_urban == 2) {
            $blockname = optional($this->block)->name;
            $gpname = optional($this->panchayat)->name;
        } else {
            $blockname = optional($this->municipality)->name;
            $gpname = optional($this->ward)->name;
        }
        return [
            'block' => $blockname ? strtoupper($blockname) : '',
            'gp' => $gpname ? strtoupper($gpname) : ''
        ];
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
        return $this->belongsTo(District::class, 'district_id', 'id');
    }
    public function subdivision()
    {
        return $this->belongsTo(Subdivision::class, 'sub_division_id', 'id');
    }   
}
