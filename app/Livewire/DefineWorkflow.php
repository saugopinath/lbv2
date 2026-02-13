<?php

namespace App\Livewire;

use Livewire\Component;

class DefineWorkflow extends Component
{
    public $schemeId;
    public $steps = [];
    public $currentStep = 0; // Index-based (0,1,2...)

    public function mount($schemeId)
    {
        $this->schemeId = $schemeId;

        $this->steps = [
            [
                'title' => 'Create Workflow Steps',
                'description' => 'Define the number of steps required in the workflow process.',
                'component' => 'createworkflow-steps',
                'step' => 1,
            ],
            [
                'title' => 'Duplicate Check Configuration',
                'description' => 'Set rules to prevent duplicate entries.',
                'component' => 'dup-check-scheme-config-settings',
                'step' => 2, // Only for display
            ],
            [
                'title' => 'Age Management Configuration',
                'description' => 'Define age validation rules and eligibility.',
                'component' => 'age-management',
                'step' => 3, // Only for display
            ],
        ];
    }

    public function render()
    {
        return view('livewire.define-workflow');
    }
}
