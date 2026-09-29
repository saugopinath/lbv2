<?php

namespace App\Livewire;

use App\Models\Scheme;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Helpers\WorkFlowPermissionHelper;

class Form extends Component
{
    public bool $schemeData = false;
    public $schemeId, $schemeName = null;
    public $showSchemeDropdown = true;
    public $grievanceId;
    public $moduleCode;
    public function mount($moduleCode = null, $hideSchemeDropdown = false)
    {
        $this->moduleCode = $moduleCode;
        if ($hideSchemeDropdown) {
            $this->showSchemeDropdown = false;
            $schemeData = Scheme::where('is_active', 1)->first();
            $this->schemeId = $schemeData->id;
            $this->schemeData = true;
            $this->schemeName = $schemeData->name;
        }
        if (request()->has('id')) {
            $this->grievanceId = request()->query('id');
        }
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
            if ($this->moduleCode && !WorkFlowPermissionHelper::canAccessModule($this->moduleCode, $selectedSchemeId)) {
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
        return view('livewire.form');
    }
}
