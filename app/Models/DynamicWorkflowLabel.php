<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DynamicWorkflowLabel extends BaseAuditableModel
{
    protected $table = 'dynamic_workflow_labels';
    
    protected $fillable = [
        'scheme_id',
        'module_id',
        'label_name'
    ];

    public function module()
    {
        return $this->belongsTo(DynamicWorkflowModule::class, 'module_id');
    }
}
