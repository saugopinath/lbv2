<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
class ApplicationLists extends Component
{
    public bool $schemeData = false;
    public $schemeId, $schemeName = null;
    public $moduleCode = null;

    public function mount($moduleCode = null)
    {
        $this->moduleCode = $moduleCode;
    }

    /* OLD CODE COMMENTED OUT FOR BACKWARD COMPATIBILITY:
    #[On('selectedScheme')]
    public function updateschemeData($schemeData)
    {
        if ($schemeData) {
            $this->schemeData = true;
            $this->schemeId = $schemeData['scheme_id'];
            $this->schemeName = $schemeData['scheme_name'];
        } else {
            $this->schemeData = false;
        }
    }
    */

    #[On('selectedScheme')]
    public function updateschemeData($schemeData)
    {
        if ($schemeData) {
            $selectedSchemeId = $schemeData['scheme_id'];
            if (!empty($schemeData['module_code'])) {
                $this->moduleCode = $schemeData['module_code'];
            }
            if ($this->moduleCode && !\App\Helpers\WorkFlowPermissionHelper::canAccessModule($this->moduleCode, $selectedSchemeId)) {
                $this->schemeData = false;
                $this->dispatch('toastr', [
                    'type' => 'error',
                    'message' => 'You do not have access to this module for the selected scheme.',
                ]);
                return;
            }
            $this->schemeData = true;
            $this->schemeId = $selectedSchemeId;
            $this->schemeName = $schemeData['scheme_name'];
        } else {
            $this->schemeData = false;
        }
    }
    public function render()
    {
        return view('livewire.application-lists');
    }
}
