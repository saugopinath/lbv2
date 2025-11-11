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
    public function subdivision()
    {
        return $this->belongsTo(Subdivision::class, 'sub_division_id', 'id');
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
            $parts[] = "District - " . strtoupper($district);
        }

        // Rural
        if ($this->rural_urban_id == 2) {
            if ($block) {
                $parts[] = "Block - " . strtoupper($block);
            }
            if ($panchayat) {
                $parts[] = "GP - " . strtoupper($panchayat);
            }
        }
        // Urban
        else {
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
        $block = optional($this->block)->name;
        $panchayat = optional($this->panchayat)->name;
        $municipality = optional($this->municipality)->name;
        $ward = optional($this->ward)->name;
        $blockname = '';
        $gpname = '';
        if ($this->rural_urban_id == 2) {
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
