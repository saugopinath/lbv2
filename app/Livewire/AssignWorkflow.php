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
        $schemeModule = \App\Models\DynamicWorkflowSchemeModule::where('scheme_id', $schemeId)
            ->where('module_id', $moduleId)
            ->first();

        if ($schemeModule) {
            $steps = DynamicWorkflowLabel::where('scheme_id', $schemeId)
                ->where('module_id', $schemeModule->id)
                ->get();
            if ($steps->isNotEmpty()) {
                $this->already = true;
            }
        }
    }
    public function render()
    {
        return view('livewire.assign-workflow');
    }
}
