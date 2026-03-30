<?php

namespace App\Livewire\CasteManagement;

use Livewire\Component;

class ModifyCasteManagement extends Component
{
    public bool    $schemeData         = false;
    public bool    $showTable          = false;
    public ?int    $schemeId           = null;
    public ?string $schemeName         = null;
    public ?string $moduleCode         = null;
    public ?string $moduleName         = null;
    public ?int    $selectedModuleId   = null;   // This will store the scheme_module_id
    public ?string $selectedModuleCode = null;
    public ?string $selectedModuleName = null;
    public ?int    $selectedStepId     = null;
    public ?int    $confirmedStepId    = null;
    public ?string $selectedStepName   = null;
    public ?string $stage = null;
    public $mainModuleId = null;
    public array $stepOptions = []; // Changed from moduleOptions to stepOptions

    protected ?int $userRoleId = null;

    public function mount($moduleCode = null, $moduleName = null, $mainModuleId = null)
    {
        if ($moduleCode) {
            $this->moduleCode = $moduleCode;
        }
        if ($moduleName) {
            $this->moduleName = $moduleName;
        }
        if ($mainModuleId) {
            $this->mainModuleId = $mainModuleId;
        }
    }
    public function render()
    {
        return view('livewire.caste-management.modify-caste-management');
    }
}
