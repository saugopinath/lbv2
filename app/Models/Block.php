<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Block extends Model
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

    public function panchayats()
    {
        return $this->hasMany(Panchayat::class);
    }
}
