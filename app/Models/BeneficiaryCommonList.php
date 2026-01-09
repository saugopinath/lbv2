<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use OwenIt\Auditing\Contracts\Auditable;

class BeneficiaryCommonList extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    protected $guarded = [];
    protected $primaryKey = 'sourceable_id';
    protected $table = 'lb_scheme.beneficiary_common_lists';
    public function sourceable()
    {
        return $this->morphTo();
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'cd_district_id', 'id');
    }
    public function block()
    {
        return $this->belongsTo(Block::class, 'cd_block_muni_id');
    }
    public function panchayat()
    {
        return $this->belongsTo(Panchayat::class, 'cd_gp_ward_id');
    }
    public function ward()
    {
        return $this->belongsTo(Ward::class, 'cd_gp_ward_id');
    }
    public function municipality()
    {
        return $this->belongsTo(Municipality::class, 'cd_block_muni_id', 'id');
    }
    public function getFullAddress(): string
    {
        $district = optional($this->district)->name;
        $subdivision = optional($this->municipality?->Subdivision)->name;
        $block = optional($this->block)->name;
        $panchayat = optional($this->panchayat)->name;
        $municipality = optional($this->municipality)->name;
        $ward = optional($this->ward)->name;
        $parts = [];
        if ($district) {
            $parts[] = "District - " . strtoupper($district);
        }
        // Rural
        if ($this->cd_rural_urban_id == 2) {
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
        return !empty($parts) ? implode('<br>', $parts) : 'N/A';
    }
}
