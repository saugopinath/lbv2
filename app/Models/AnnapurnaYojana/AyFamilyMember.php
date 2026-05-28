<?php

namespace App\Models\AnnapurnaYojana;

use Illuminate\Database\Eloquent\Model;

class AyFamilyMember extends Model
{
    protected $connection = 'pgsql_ay';
    protected $table      = 'family_members';
    protected $primaryKey = 'id';
    public    $timestamps = false;

    protected $casts = [
        'is_hof'                    => 'boolean',
        'is_child'                  => 'boolean',
        'has_four_wheeler'          => 'boolean',
        'has_health_insurance'      => 'boolean',
        'has_pan_card'              => 'boolean',
        'has_three_pucca_rooms'     => 'boolean',
        'owns_land'                 => 'boolean',
        'is_govt_pensioner'         => 'boolean',
        'holds_constitutional_post' => 'boolean',
        'is_registered_gst'         => 'boolean',
        'pays_income_or_professional_tax' => 'boolean',
        'applying_for_annapurna_bhandar'  => 'boolean',
    ];

    public function family(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AyFamily::class, 'family_id');
    }

    public function employmentNatures(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AyMemberEmploymentNature::class, 'member_id');
    }

    public function govtSchemes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AyMemberGovtScheme::class, 'member_id');
    }
}
