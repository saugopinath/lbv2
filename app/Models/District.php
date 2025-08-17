<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
     protected $fillable = [
        'name',
        'ref_code',
        'lgd_code',
        'short_name',
        'state_id',
    ];
    public function State(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }
    public function blocks()
    {
        return $this->hasMany(Block::class);
    }

    public function municipalities()
    {
        return $this->hasMany(Municipality::class);
    }
    
    /*public function panchayats()
    {
        return $this->hasManyThrough(Panchayat::class, Block::class);
    }*/
}
