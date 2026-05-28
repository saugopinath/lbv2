<?php

namespace App\Models\AnnapurnaYojana;

use Illuminate\Database\Eloquent\Model;

class AyFamily extends Model
{
    protected $connection = 'pgsql_ay';
    protected $table     = 'families';
    protected $primaryKey = 'id';

    protected $casts = [
        'lifting_monthly_ration'    => 'boolean',
        'has_electricity_connection'=> 'boolean',
        'is_agreed'                 => 'boolean',
        'has_digital_ration_card'   => 'boolean',
        'created_at'                => 'datetime',
        'updated_at'                => 'datetime',
    ];

    /** Head of household member */
    public function headOfFamily(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(AyFamilyMember::class, 'family_id')->where('is_hof', true);
    }

    /** All family members */
    public function members(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AyFamilyMember::class, 'family_id');
    }
}
