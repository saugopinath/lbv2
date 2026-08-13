<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Scheme;
use Livewire\Attributes\On;

class ProcessWorkflow extends Component
{
    public bool $schemeData = false;
    public $schemeId, $schemeName = null;
    public $showSchemeDropdown = true;
    public $header;
    public $moduleCode;
    public function mount($hideSchemeDropdown = false)
    {

        if ($hideSchemeDropdown) {
            $this->showSchemeDropdown = false;
            $schemeData = Scheme::where('is_active', 1)->first();
            $this->schemeId = $schemeData->id;
            $this->schemeData = true;
            $this->schemeName = $schemeData->name;
        }
        $this->moduleCode = config('constants.module_codes.update_mark_beneficiary');
        $this->header = "Update Mark Beneficiary Details";
    }
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
    public function render()
    {
        return view('livewire.process-workflow');
    }
}
