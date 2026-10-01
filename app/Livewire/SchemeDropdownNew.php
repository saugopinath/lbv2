<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Crypt;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\{Scheme, DynamicWorkflowModule, DynamicWorkflowSchemeModule, DynamicWorkflowLabel};
use App\Helpers\WorkFlowPermissionHelper;

class SchemeDropdownNew extends Component
{
    public $isFinal = false;
    public $isAssigned = false;
    public $enableModuleSelection = false;
    public $schemes = [];
    public $modules = [];
    public $schemeId;
    public $moduleId;
    public $schemeSelected = false;
    public $moduleCode = null;
    public bool $showNotConfiguredModal = false;
    public string $notConfiguredMessage = '';

    #[On('resetSchemeDropdown')]
    public function resetDropdown()
    {
        $this->reset(['schemeId', 'moduleId', 'schemeSelected', 'showNotConfiguredModal', 'notConfiguredMessage']);
    }

    /* OLD CODE COMMENTED OUT FOR BACKWARD COMPATIBILITY:
    #[On('scheme-created')]
    public function mount($isFinal = false, $isAssigned = false, $moduleCode = null)
    {
        $this->moduleCode = $moduleCode;
        $scheme_id = null;
        if ($isAssigned) {
            $select_lgd = session('lgd_session');

            if (!empty($select_lgd['scheme_id'])) {
                $scheme_id = Crypt::decryptString($select_lgd['scheme_id'][0]);
            }
        }

        $query = Scheme::select('id', 'name')
            ->where('is_active', 1)
            ->when($scheme_id, fn($q) => $q->where('id', $scheme_id));

        if (!empty($this->moduleCode)) {
            $module = DynamicWorkflowModule::select('id')->where('module_code', $this->moduleCode)->first();
            if ($module) {
                $configuredSchemeIds = DynamicWorkflowSchemeModule::where('module_id', $module->id)
                    ->where('is_disabled', 0)
                    ->pluck('scheme_id')
                    ->toArray();
                $query->whereIn('id', $configuredSchemeIds);
            }
        }

        $allSchemes = $query->get();

        if (!empty($this->moduleCode)) {
            $this->schemes = $allSchemes->filter(function ($sch) {
                return WorkFlowPermissionHelper::canAccessModule($this->moduleCode, $sch->id);
            })->values();
        } else {
            $this->schemes = $allSchemes;
        }
    }
    */

    #[On('scheme-created')]
    public function mount($isFinal = false, $isAssigned = false, $moduleCode = null, $enableModuleSelection = false)
    {
        $this->isFinal = $isFinal;
        $this->isAssigned = $isAssigned;
        $this->moduleCode = $moduleCode;
        $this->enableModuleSelection = $enableModuleSelection;

        $duty = WorkFlowPermissionHelper::getCurrentDuty();
        $user = auth()->user();

        $query = Scheme::select('id', 'name')->where('is_active', 1);

        // Duty & user scheme scope filtering for assigned views
        if ($this->isAssigned && $user) {
            $assignedSchemeIds = [];

            if (!empty($duty['office_id']) && !empty($duty['role_id'])) {
                $assignedSchemeIds = \App\Models\UserRoleSchemeOfficeMapping::where('user_id', $user->id)
                    ->where('office_id', $duty['office_id'])
                    ->where('role_id', $duty['role_id'])
                    ->pluck('scheme_id')
                    ->filter()
                    ->unique()
                    ->toArray();
            }

            if (empty($assignedSchemeIds)) {
                $select_lgd = session('lgd_session');
                if (!empty($select_lgd['scheme_id'])) {
                    if (is_array($select_lgd['scheme_id'])) {
                        foreach ($select_lgd['scheme_id'] as $enc) {
                            try {
                                $assignedSchemeIds[] = (int) Crypt::decryptString($enc);
                            } catch (\Exception $e) {
                                // Ignore decrypt failure
                            }
                        }
                    } else {
                        try {
                            $assignedSchemeIds[] = (int) Crypt::decryptString($select_lgd['scheme_id']);
                        } catch (\Exception $e) {}
                    }
                }
            }

            if (!empty($assignedSchemeIds)) {
                $query->whereIn('id', $assignedSchemeIds);
            }
        }

        if (!empty($this->moduleCode)) {
            $module = DynamicWorkflowModule::select('id')->where('module_code', $this->moduleCode)->first();
            if ($module) {
                $configuredSchemeIds = DynamicWorkflowSchemeModule::where('module_id', $module->id)
                    ->where('is_disabled', 0)
                    ->pluck('scheme_id')
                    ->toArray();
                $query->whereIn('id', $configuredSchemeIds);
            }
        }

        $allSchemes = $query->orderBy('name')->get();

        if (!empty($this->moduleCode)) {
            $this->schemes = $allSchemes->filter(function ($sch) {
                return WorkFlowPermissionHelper::canAccessModule($this->moduleCode, $sch->id);
            })->values();
        } else {
            $this->schemes = $allSchemes;
        }

        if ($this->enableModuleSelection) {
            $this->modules = DynamicWorkflowModule::select('id', 'module_name', 'module_code')
                ->where('is_active', 1)
                ->orderBy('module_name')
                ->get();
        }
    }

    public function updatedSchemeId($value)
    {
        $this->moduleId = null;
        if ($value) {
            $this->schemeSelected = true;
            if (!$this->enableModuleSelection) {
                $scheme = collect($this->schemes)->firstWhere('id', (int) $value);
                $schemeName = data_get($scheme, 'name');
                $schemeData = [
                    'scheme_id' => (int) $value,
                    'scheme_name' => $schemeName,
                    'module_code' => $this->moduleCode,
                ];
                $this->dispatch('selectedScheme', $schemeData);
            } else {
                // Wait for module selection
                $this->dispatch('selectedScheme', null);
            }
        } else {
            $this->schemeSelected = false;
            $this->dispatch('selectedScheme', null);
        }
    }

    public function updatedModuleId($value)
    {
        if ($value && $this->schemeId) {
            $module = collect($this->modules)->firstWhere('id', (int) $value);
            if (!$module) {
                return;
            }

            // Check if workflow is configured for this scheme and module
            $schemeModule = DynamicWorkflowSchemeModule::select('id')
                ->where('scheme_id', (int) $this->schemeId)
                ->where('module_id', (int) $value)
                ->where('is_disabled', 0)
                ->first();

            $hasSteps = false;
            if ($schemeModule) {
                $hasSteps = DynamicWorkflowLabel::where('module_id', $schemeModule->id)
                    ->where('scheme_id', (int) $this->schemeId)
                    ->exists();
            }

            if (!$schemeModule || !$hasSteps) {
                $scheme = collect($this->schemes)->firstWhere('id', (int) $this->schemeId);
                $schemeName = data_get($scheme, 'name', 'Selected Scheme');
                $moduleName = data_get($module, 'module_name', 'Selected Module');

                $this->notConfiguredMessage = "The workflow for \"{$schemeName}\" under \"{$moduleName}\" has not been configured yet. Please contact the administrator to define the workflow steps.";
                $this->showNotConfiguredModal = true;
                $this->moduleId = null;
                $this->dispatch('selectedScheme', null);
                return;
            }

            // Module permission access check
            $moduleCode = data_get($module, 'module_code');
            if (!WorkFlowPermissionHelper::canAccessModule($moduleCode, (int) $this->schemeId)) {
                $this->notConfiguredMessage = "You do not have permission to access the \"{$module->module_name}\" workflow for this scheme.";
                $this->showNotConfiguredModal = true;
                $this->moduleId = null;
                $this->dispatch('selectedScheme', null);
                return;
            }

            $scheme = collect($this->schemes)->firstWhere('id', (int) $this->schemeId);
            $schemeName = data_get($scheme, 'name');

            $this->dispatch('selectedScheme', [
                'scheme_id'   => (int) $this->schemeId,
                'scheme_name' => $schemeName,
                'module_id'   => (int) $module->id,
                'module_code' => $module->module_code,
                'module_name' => $module->module_name,
            ]);
        } else {
            $this->dispatch('selectedScheme', null);
        }
    }

    public function closeNotConfiguredModal()
    {
        $this->showNotConfiguredModal = false;
        $this->moduleId = null;
    }

    public function render()
    {
        return view('livewire.scheme-dropdown-new');
    }
}
