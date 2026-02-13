<?php

namespace App\Livewire;

use App\Models\Scheme;
use Livewire\Component;

class DefineWorkflow extends Component
{
    public $schemeId, $schemeName;
    public $steps = [];
    public $currentStep = 0;
    public function mount($schemeId)
    {
        $this->schemeId = $schemeId;
        $this->schemeName = ucwords(strtolower(Scheme::find($schemeId)->name));
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
