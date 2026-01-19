<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Scheme extends Model
{
    protected $table = 'schemes';
    protected $fillable = [
        'name',
        'short_name',
        'description',
        'department_id',
        'is_active'
    ];


    public function Department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
    public function finalSubmitChecks()
    {
        return $this->hasMany(SchemeFinalSubmitCheck::class, 'scheme_id');
    }
}
