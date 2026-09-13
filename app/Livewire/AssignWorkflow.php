<?php

namespace App\Livewire;

use App\Models\DynamicWorkflowLabel;
use Livewire\Component;

class AssignWorkflow extends Component
{
    public $schemeId;
    public $moduleId;
    public bool $already = false;
    public function mount($schemeId, $moduleId)
    {
        $this->schemeId = $schemeId;
        $this->moduleId = $moduleId;
        // FIX: Check DynamicWorkflowLabel instead of WorkflowStep since new steps are saved there
        $steps = DynamicWorkflowLabel::where('scheme_id', $schemeId)
            ->where('module_id', $moduleId)
            ->get();
        if ($steps->isNotEmpty()) {
            $this->already = true;
        }
    }
    public function render()
    {
        return view('livewire.assign-workflow');
    }
}
