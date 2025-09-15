<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ValidationScoreMapping extends Model
{
    protected $fillable = [
        'permission_id',
        'min_score',
        'max_score',
    ];
    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }
}
