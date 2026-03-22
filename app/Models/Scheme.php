<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Scheme extends Model
{
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

    public function workflowSteps()
    {
        return $this->hasMany(WorkflowStep::class, 'scheme_id');
    }
    public function capacities()
    {
        return $this->morphMany(SchemeCapacity::class, 'modelable', 'model_type', 'model_id');
    }
    public function schemeFinalSubmitChecks()
    {
        return $this->hasMany(SchemeFinalSubmitCheck::class);
    }
}
