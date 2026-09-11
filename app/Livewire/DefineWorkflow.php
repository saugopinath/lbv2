<?php

namespace App\Livewire;

use App\Models\DynamicWorkflowModule;
use App\Models\Scheme;
use Livewire\Component;

class DefineWorkflow extends Component
{
    public $schemeId;
    public $schemeName;
    public $steps = [];
    public $currentStep = 0;
    public $moduleId;
    public $moduleName;
    public $moduleData;
    public $schemeData;
    public function mount($schemeData, $moduleData)
    {
        $this->schemeId = $schemeData['scheme_id'];
        $this->moduleId = $moduleData['module_id'];
        $this->moduleName = $moduleData['module_name'];
        $this->schemeName = $schemeData['scheme_name'];
        $this->moduleData = $moduleData;
        $this->schemeData = $schemeData;
        // $this->schemeId = $schemeId;
        // $this->moduleId = $moduleId;
        // $this->moduleName = ucwords(strtolower(DynamicWorkflowModule::find($moduleId)->module_name));;
        // $this->schemeName = ucwords(strtolower(Scheme::find($schemeId)->name));
        $this->steps = [
            [
                'title' => 'Create Workflow Steps',
                'description' => 'Define the number of steps required in the workflow process.',
                'component' => 'createworkflow-steps',
                'step' => 1,
            ],
            [
                'title' => 'Assign Role to Steps',
                'description' => 'Assign specific roles to each workflow step.',
                'component' => 'assign-workflow',
                'step' => 2,
            ],
            [
                'title' => 'Duplicate Check Configuration',
                'description' => 'Set rules to prevent duplicate entries.',
                'component' => 'dup-check-scheme-config-settings',
                'step' => 3,
            ],
            [
                'title' => 'Age Management Configuration',
                'description' => 'Define age validation rules and eligibility.',
                'component' => 'age-management',
                'step' => 4,
            ],
        ];
    }
    public function render()
    {
        return view('livewire.define-workflow');
    }
}
