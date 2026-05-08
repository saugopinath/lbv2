<?php

namespace App\Livewire\CasteManagement;

use App\Models\DynamicWorkflowLabel;
use App\Models\DynamicWorkflowModule;
use App\Models\DynamicWorkflowSchemeModule;
use App\Models\UserRoleSchemeOficeMapping;
use App\Models\workflowstepRolemapping;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Livewire\Attributes\On;
use Livewire\Component;

class CasteModificationWorkflowList extends Component
{
    public bool    $schemeData         = false;
    public bool    $showTable          = false;
    public ?int    $schemeId           = null;
    public ?string $schemeName         = null;
    public ?string $moduleCode         = null;
    public ?string $moduleName         = null;
    public ?int    $selectedModuleId   = null;
    public ?string $selectedModuleCode = null;
    public ?string $selectedModuleName = null;
    public ?int    $selectedStepId     = null;
    public ?int    $confirmedStepId    = null;
    public ?string $selectedStepName   = null;
    public array $stepOptions = [];
    public int $userRoleId = 0;

    public function mount($moduleCode = null, $moduleName = null)
    {
        if ($moduleCode) {
            $this->moduleCode = $moduleCode;
        }
        if ($moduleName) {
            $this->moduleName = $moduleName;
        } else {
            $this->moduleName = DynamicWorkflowModule::where('module_code', $this->moduleCode)->value('module_name');
        }
        $lgd = session('lgd_session');
        if (!empty($lgd['role_id'])) {
            try {
                $this->userRoleId = (int) Crypt::decryptString($lgd['role_id']);
            } catch (\Exception) {
            }
        }
    }

    #[On('selectedScheme')]
    public function updateschemeData($schemeData)
    {
        if ($schemeData) {
            $this->schemeData         = true;
            $this->schemeId           = (int) $schemeData['scheme_id'];
            $this->schemeName         = $schemeData['scheme_name'];
            $this->showTable          = false;
            $this->selectedModuleId   = null;
            $this->selectedModuleCode = null;
            $this->selectedStepId     = null;
            $this->selectedStepName   = null;

            $this->loadStepOptions();
        } else {
            $this->reset(['schemeData', 'schemeId', 'schemeName', 'stepOptions', 'showTable', 'selectedStepId', 'selectedStepName']);
        }
    }

    public function loadStepOptions(): void
    {
        $mainModuleId = DynamicWorkflowModule::where('module_code', $this->moduleCode)->value('id');
        if (!$mainModuleId) {
            $this->stepOptions = [];
            return;
        }
        $sm = DynamicWorkflowSchemeModule::where('module_id', $mainModuleId)
            ->where('scheme_id', $this->schemeId)
            ->first();
        if (!$sm) {
            $this->stepOptions = [];
            return;
        }
        $this->selectedModuleId   = $sm->id;
        $this->selectedModuleCode = $this->moduleCode;
        $this->selectedModuleName = $sm->module?->module_name ?? $sm->main_module_code;
        $this->stepOptions = DynamicWorkflowLabel::where('module_id', $sm->id)
            ->where('scheme_id', $this->schemeId)
            ->pluck('label_name', 'id')
            ->toArray();

        // Add special options
        // $this->stepOptions[-1] = 'Rejected List';
        $this->stepOptions[-$this->schemeId] = 'Reverted List';

        if (count($this->stepOptions) === 1) {
            $this->selectedStepId = array_key_first($this->stepOptions);
        }
    }

    public function confirmSearch(): void
    {
        $this->validate([
            'selectedStepId' => 'required'
        ], [
            'selectedStepId.required' => 'Please select a workflow step.'
        ]);

        if (array_key_exists($this->selectedStepId, $this->stepOptions)) {
            $this->confirmedStepId = $this->selectedStepId;
            $this->selectedStepName = $this->stepOptions[$this->selectedStepId];
            $this->showTable = true;
        } else {
            $this->dispatch('toastr', ['type' => 'error', 'message' => 'Selected step not found.']);
        }
    }

    public function changeScheme(): void
    {
        $this->reset(['schemeData', 'schemeId', 'schemeName', 'stepOptions', 'showTable', 'selectedModuleId', 'selectedModuleCode', 'selectedStepId', 'selectedStepName']);
        $this->dispatch('resetSchemeDropdown');
    }

    public function render()
    {
        return view('livewire.caste-management.caste-modification-workflow-list');
    }
}
