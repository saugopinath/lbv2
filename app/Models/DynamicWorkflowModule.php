<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DynamicWorkflowModule extends BaseAuditableModel
{
    protected $table = 'dynamic_workflow_modules';
    
    protected $fillable = [
        'scheme_id',
        'module_code',
        'module_name',
        'allowed_fields',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'allowed_fields' => 'array',
        'is_active' => 'boolean'
    ];

    public function steps()
    {
        return $this->hasMany(workflowstepRolemapping::class, 'module_id')->orderBy('rank');
    }

    public function scheme()
    {
        return $this->belongsTo(Scheme::class, 'scheme_id');
    }
}
