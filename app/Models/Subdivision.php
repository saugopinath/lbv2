<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subdivision extends Model
{
    protected $fillable = [
        'name',
        'ref_code',
        'lgd_code',
        'district_id',
        'state_id',
    ];

    public function District(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    // public function district()
    // {
    //     return $this->belongsTo(District::class);
    // }

    public function municipalities()
    {
        return $this->hasMany(Municipality::class);
    }
}
