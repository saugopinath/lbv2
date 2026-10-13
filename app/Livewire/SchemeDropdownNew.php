<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Crypt;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\{Scheme, DynamicWorkflowModule, DynamicWorkflowSchemeModule, DynamicWorkflowLabel, OfficeMaster};
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
        $this->reset(['schemeId', 'moduleId', 'schemeSelected', 'showNotConfiguredModal', 'notConfiguredMessage', 'modules']);
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
        $isSuperAdmin = WorkFlowPermissionHelper::isSuperAdmin($user);

        $configuredSchemeIds = [];
        if (!empty($this->moduleCode)) {
            $moduleId = DynamicWorkflowModule::where('module_code', $this->moduleCode)->value('id');
            if ($moduleId) {
                $configuredSchemeIds = DynamicWorkflowSchemeModule::where('module_id', $moduleId)
                    ->where('is_disabled', 0)
                    ->pluck('scheme_id')
                    ->toArray();
            }
        }

        if ($isSuperAdmin) {
            // Superadmin has unrestricted access to all active schemes
            $query = Scheme::select('id', 'name')->where('is_active', 1);

            if (!empty($configuredSchemeIds)) {
                $query->whereIn('id', $configuredSchemeIds);
            }

            $this->schemes = $query->orderBy('name')->get();
        } else {
            $cachedSchemes = collect();

            if (!empty($duty['scheme_list'])) {
                $rawList = is_string($duty['scheme_list']) ? json_decode($duty['scheme_list'], true) : $duty['scheme_list'];
                $cachedSchemes = collect($rawList)
                    ->filter(fn($s) => ((int) data_get($s, 'is_active', 1)) === 1)
                    ->map(fn($s) => (object) [
                        'id'   => (int) data_get($s, 'id'),
                        'name' => (string) data_get($s, 'name'),
                    ])
                    ->unique('id')
                    ->values();
            }

            if ($cachedSchemes->isNotEmpty()) {
                if (!empty($this->moduleCode)) {
                    if (!empty($configuredSchemeIds)) {
                        $cachedSchemes = $cachedSchemes->filter(fn($sch) => in_array($sch->id, $configuredSchemeIds));
                    }

                    $this->schemes = $cachedSchemes->filter(function ($sch) {
                        return WorkFlowPermissionHelper::canAccessModule($this->moduleCode, $sch->id);
                    })->sortBy('name')->values();
                } else {
                    $this->schemes = $cachedSchemes->sortBy('name')->values();
                }
            } else {
                $this->schemes = Scheme::select('id', 'name')
                    ->where('is_active', 1)
                    ->orderBy('name')
                    ->get();
            }
        }

        if ($this->enableModuleSelection && $this->schemeId) {
            $this->loadModulesForScheme($this->schemeId);
        } else {
            $this->modules = [];
        }
    }

    public function updatedSchemeId($value)
    {
        $this->moduleId = null;
        $this->modules = [];
        if ($value) {
            $this->schemeSelected = true;
            if ($this->enableModuleSelection) {
                // Load all modules accessible to current user for this scheme
                $this->loadModulesForScheme($value);

                // If user has access to more than 1 module under this scheme, prompt them with module dropdown
                if (count($this->modules) > 1) {
                    return;
                }

                // If exactly 1 module is accessible, auto-resolve directly to it
                if (count($this->modules) === 1) {
                    $singleModule = collect($this->modules)->first();
                    $singleModuleId = data_get($singleModule, 'id');
                    $this->resolveModuleForScheme($value, $singleModuleId);
                    return;
                }

                // If 0 modules found, trigger not-configured workflow modal
                $this->resolveModuleForScheme($value);
            } else {
                $scheme = collect($this->schemes)->firstWhere('id', (int) $value);
                $schemeName = data_get($scheme, 'name');
                $schemeData = [
                    'scheme_id' => (int) $value,
                    'scheme_name' => $schemeName,
                    'module_code' => $this->moduleCode,
                ];
                $this->dispatch('selectedScheme', $schemeData);
            }
        } else {
            $this->schemeSelected = false;
            $this->dispatch('selectedScheme', null);
        }
    }

    /**
     * Resolves the appropriate workflow module for the selected scheme automatically
     * based on target module ID or active duty context (Block = rural, Subdivision = urban, others = normal).
     */
    public function resolveModuleForScheme($schemeId, $targetModuleId = null)
    {
        $duty = WorkFlowPermissionHelper::getCurrentDuty();
        $officeType = $duty['office_type'] ?? null;
        $targetWorkflowType = 'normal';

        if ($officeType === 'block') {
            $targetWorkflowType = 'rural';
        } elseif ($officeType === 'subdivision') {
            $targetWorkflowType = 'urban';
        } else {
            $officeId = $duty['office_id'] ?? null;
            if ($officeId) {
                $office = OfficeMaster::with(['officeType' => fn($q) => $q->select('id', 'name', 'short_name')])
                    ->select('id', 'office_type_id', 'block_id', 'subdivision_id', 'municipalitiy_id')
                    ->find($officeId);
                if ($office) {
                    if ((int) $office->office_type_id === 153 || !empty($office->block_id)) {
                        $targetWorkflowType = 'rural';
                    } elseif ((int) $office->office_type_id === 154 || !empty($office->subdivision_id) || !empty($office->municipalitiy_id)) {
                        $targetWorkflowType = 'urban';
                    } else {
                        $typeShort = strtolower($office->officeType?->short_name ?? $office->officeType?->name ?? '');
                        if (str_contains($typeShort, 'block') || str_contains($typeShort, 'bdo') || str_contains($typeShort, 'rural')) {
                            $targetWorkflowType = 'rural';
                        } elseif (str_contains($typeShort, 'sdo') || str_contains($typeShort, 'subdiv') || str_contains($typeShort, 'muni') || str_contains($typeShort, 'urban')) {
                            $targetWorkflowType = 'urban';
                        } else {
                            $targetWorkflowType = 'normal';
                        }
                    }
                }
            }
        }

        // Fetch all active scheme modules configured for this scheme
        $schemeModules = DynamicWorkflowSchemeModule::with(['module' => fn($q) => $q->select('id', 'module_name', 'module_code', 'workflow_type')])
            ->select('id', 'scheme_id', 'module_id', 'main_module_code', 'is_disabled')
            ->where('scheme_id', (int) $schemeId)
            ->where('is_disabled', 0)
            ->get();

        if ($schemeModules->isEmpty()) {
            $scheme = collect($this->schemes)->firstWhere('id', (int) $schemeId);
            $schemeName = data_get($scheme, 'name', 'Selected Scheme');
            $this->notConfiguredMessage = "The workflow for \"{$schemeName}\" has not been configured yet. Please contact the administrator.";
            $this->showNotConfiguredModal = true;
            $this->dispatch('selectedScheme', null);
            return null;
        }

        if ($targetModuleId) {
            $matched = $schemeModules->firstWhere('module_id', (int) $targetModuleId);
        } else {
            // 1. Try matching target workflow_type (e.g. rural or urban)
            $matched = $schemeModules->first(function ($sm) use ($targetWorkflowType) {
                return strtolower($sm->module?->workflow_type ?? 'normal') === $targetWorkflowType;
            });

            // 2. Fallback to 'normal' if target type was rural/urban but not found
            if (!$matched && $targetWorkflowType !== 'normal') {
                $matched = $schemeModules->first(function ($sm) {
                    return strtolower($sm->module?->workflow_type ?? 'normal') === 'normal';
                });
            }

            // 3. Fallback to single/first active module configured
            if (!$matched) {
                $matched = $schemeModules->first();
            }
        }

        if (!$matched) {
            $scheme = collect($this->schemes)->firstWhere('id', (int) $schemeId);
            $schemeName = data_get($scheme, 'name', 'Selected Scheme');
            $this->notConfiguredMessage = "The workflow for \"{$schemeName}\" has not been configured yet. Please contact the administrator.";
            $this->showNotConfiguredModal = true;
            $this->dispatch('selectedScheme', null);
            return null;
        }

        // Verify that steps exist for this workflow
        $hasSteps = DynamicWorkflowLabel::where('module_id', $matched->id)
            ->where('scheme_id', (int) $schemeId)
            ->exists();

        if (!$hasSteps) {
            $scheme = collect($this->schemes)->firstWhere('id', (int) $schemeId);
            $schemeName = data_get($scheme, 'name', 'Selected Scheme');
            $moduleName = $matched->module?->module_name ?? 'Selected Module';
            $this->notConfiguredMessage = "The workflow for \"{$schemeName}\" under \"{$moduleName}\" has not been configured with steps yet. Please contact the administrator.";
            $this->showNotConfiguredModal = true;
            $this->dispatch('selectedScheme', null);
            return null;
        }

        // Permission check (SuperAdmin always bypasses)
        $moduleCode = $matched->module?->module_code ?? $matched->main_module_code;
        if (!WorkFlowPermissionHelper::isSuperAdmin() && !WorkFlowPermissionHelper::canAccessModule($moduleCode, (int) $schemeId)) {
            $moduleName = $matched->module?->module_name ?? 'Selected Module';
            $this->notConfiguredMessage = "You do not have permission to access the \"{$moduleName}\" workflow for this scheme.";
            $this->showNotConfiguredModal = true;
            $this->dispatch('selectedScheme', null);
            return null;
        }

        $scheme = collect($this->schemes)->firstWhere('id', (int) $schemeId);
        $schemeName = data_get($scheme, 'name');

        $this->dispatch('selectedScheme', [
            'scheme_id'        => (int) $schemeId,
            'scheme_name'      => $schemeName,
            'module_id'        => (int) $matched->module_id,
            'module_code'      => $moduleCode,
            'module_name'      => $matched->module?->module_name,
            'workflow_type'    => $matched->module?->workflow_type ?? 'normal',
            'scheme_module_id' => (int) $matched->id,
        ]);

        return $matched;
    }

    public function loadModulesForScheme($schemeId)
    {
        if (empty($schemeId)) {
            $this->modules = [];
            return;
        }

        $configuredModuleIds = DynamicWorkflowSchemeModule::where('scheme_id', (int) $schemeId)
            ->where('is_disabled', 0)
            ->pluck('module_id')
            ->toArray();

        if (empty($configuredModuleIds)) {
            $this->modules = [];
            return;
        }

        $query = DynamicWorkflowModule::select('id', 'module_name', 'module_code', 'workflow_type')
            ->where('is_active', 1)
            ->whereIn('id', $configuredModuleIds)
            ->orderBy('module_name');

        $allModules = $query->get();

        if (!WorkFlowPermissionHelper::isSuperAdmin()) {
            $this->modules = $allModules->filter(function ($mod) use ($schemeId) {
                return WorkFlowPermissionHelper::canAccessModule($mod->module_code, (int) $schemeId);
            })->values();
        } else {
            $this->modules = $allModules;
        }
    }

    public function updatedModuleId($value)
    {
        if ($value && $this->schemeId) {
            $module = collect($this->modules)->firstWhere('id', (int) $value);
            if (!$module) {
                return;
            }

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

            $moduleCode = data_get($module, 'module_code');
            if (!WorkFlowPermissionHelper::isSuperAdmin() && !WorkFlowPermissionHelper::canAccessModule($moduleCode, (int) $this->schemeId)) {
                $this->notConfiguredMessage = "You do not have permission to access the \"{$module->module_name}\" workflow for this scheme.";
                $this->showNotConfiguredModal = true;
                $this->moduleId = null;
                $this->dispatch('selectedScheme', null);
                return;
            }

            $scheme = collect($this->schemes)->firstWhere('id', (int) $this->schemeId);
            $schemeName = data_get($scheme, 'name');

            $this->dispatch('selectedScheme', [
                'scheme_id'        => (int) $this->schemeId,
                'scheme_name'      => $schemeName,
                'module_id'        => (int) $module->id,
                'module_code'      => $module->module_code,
                'module_name'      => $module->module_name,
                'workflow_type'    => $module->workflow_type ?? 'normal',
                'scheme_module_id' => (int) $schemeModule->id,
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
