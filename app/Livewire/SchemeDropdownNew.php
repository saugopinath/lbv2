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

        if ($isSuperAdmin) {
            // Superadmin has unrestricted access to all active schemes
            $query = Scheme::select('id', 'name')->where('is_active', 1);

            if (!empty($this->moduleCode)) {
                $module = DynamicWorkflowModule::select('id')->where('module_code', $this->moduleCode)->first();
                if ($module) {
                    $configuredSchemeIds = DynamicWorkflowSchemeModule::where('module_id', $module->id)
                        ->where('is_disabled', 0)
                        ->pluck('scheme_id')
                        ->toArray();
                    if (!empty($configuredSchemeIds)) {
                        $query->whereIn('id', $configuredSchemeIds);
                    }
                }
            }

            $this->schemes = $query->orderBy('name')->get();
        } else {
            // Resolve assigned scheme IDs for the authenticated user
            $assignedSchemeIds = [];
            if ($user) {
                // 1. Check from active duty scheme_list
                if (!empty($duty['scheme_list'])) {
                    try {
                        $rawList = is_string($duty['scheme_list']) ? json_decode($duty['scheme_list'], true) : $duty['scheme_list'];
                        $assignedSchemeIds = collect($rawList)->map(fn($i) => (int) (is_array($i) || is_object($i) ? data_get($i, 'id') : $i))->filter()->unique()->toArray();
                    } catch (\Exception $e) {
                    }
                }

                // 2. Check from UserRoleSchemeOfficeMapping by active duty role & office
                if (empty($assignedSchemeIds) && !empty($duty['office_id']) && !empty($duty['role_id'])) {
                    $assignedSchemeIds = \App\Models\UserRoleSchemeOfficeMapping::where('user_id', $user->id)
                        ->where('office_id', $duty['office_id'])
                        ->where('role_id', $duty['role_id'])
                        ->pluck('scheme_id')
                        ->filter()
                        ->unique()
                        ->toArray();
                }

                // 3. Check all UserRoleSchemeOfficeMapping for this user
                if (empty($assignedSchemeIds)) {
                    $assignedSchemeIds = \App\Models\UserRoleSchemeOfficeMapping::where('user_id', $user->id)
                        ->pluck('scheme_id')
                        ->filter()
                        ->unique()
                        ->toArray();
                }

                // 4. Check from session lgd_session.scheme_id
                if (empty($assignedSchemeIds)) {
                    $select_lgd = session('lgd_session');
                    if (!empty($select_lgd['scheme_id'])) {
                        if (is_array($select_lgd['scheme_id'])) {
                            foreach ($select_lgd['scheme_id'] as $enc) {
                                try {
                                    $assignedSchemeIds[] = (int) Crypt::decryptString($enc);
                                } catch (\Exception $e) {
                                }
                            }
                        } else {
                            try {
                                $assignedSchemeIds[] = (int) Crypt::decryptString($select_lgd['scheme_id']);
                            } catch (\Exception $e) {
                            }
                        }
                    }
                }
            }

            // 1. FAST PATH: Attempt in-memory extraction from session $duty['scheme_list'] (0 DB queries)
            $cachedSchemes = null;
            if ($user && !empty($duty['scheme_list'])) {
                try {
                    $rawList = is_string($duty['scheme_list']) ? json_decode($duty['scheme_list'], true) : $duty['scheme_list'];
                    $collection = collect($rawList);

                    if ($collection->isNotEmpty()) {
                        $first = $collection->first();
                        // Check if collection contains Scheme models or array items with 'id' and 'name'
                        if ((is_object($first) || is_array($first)) && (data_get($first, 'id') !== null) && (data_get($first, 'name') !== null)) {
                            $cachedSchemes = $collection
                                ->filter(fn($s) => ((int) data_get($s, 'is_active', 1)) === 1)
                                ->map(fn($s) => (object) [
                                    'id'   => (int) data_get($s, 'id'),
                                    'name' => (string) data_get($s, 'name'),
                                ])
                                ->unique('id')
                                ->values();
                        }
                    }
                } catch (\Exception $e) {
                    $cachedSchemes = null;
                }
            }

            // If valid schemes were resolved directly from $duty['scheme_list'], apply module filters in-memory
            if ($cachedSchemes !== null && $cachedSchemes->isNotEmpty()) {
                if (!empty($this->moduleCode)) {
                    $module = DynamicWorkflowModule::select('id')->where('module_code', $this->moduleCode)->first();
                    if ($module) {
                        $configuredSchemeIds = DynamicWorkflowSchemeModule::where('module_id', $module->id)
                            ->where('is_disabled', 0)
                            ->pluck('scheme_id')
                            ->toArray();
                        $cachedSchemes = $cachedSchemes->filter(fn($sch) => in_array($sch->id, $configuredSchemeIds));
                    }

                    $this->schemes = $cachedSchemes->filter(function ($sch) {
                        return WorkFlowPermissionHelper::canAccessModule($this->moduleCode, $sch->id);
                    })->sortBy('name')->values();
                } else {
                    $this->schemes = $cachedSchemes->sortBy('name')->values();
                }
            } else {
                // 2. FALLBACK PATH: Standard Database Query with user & duty scope filtering
                $query = Scheme::select('id', 'name')->where('is_active', 1);

                if (!empty($assignedSchemeIds)) {
                    $query->whereIn('id', $assignedSchemeIds);
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
                // Background module auto-resolution based on session('current_duty') and workflow_type
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
     * based on active duty context (Block = rural, Subdivision = urban, others = normal).
     */
    protected function resolveModuleForScheme($schemeId)
    {
        $duty = WorkFlowPermissionHelper::getCurrentDuty();
        $officeId = $duty['office_id'] ?? null;
        $targetWorkflowType = 'normal';

        if ($officeId) {
            $office = \App\Models\OfficeMaster::with('officeType')->find($officeId);
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

        // Fetch all active scheme modules configured for this scheme
        $schemeModules = DynamicWorkflowSchemeModule::with('module')
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
            'scheme_id'     => (int) $schemeId,
            'scheme_name'   => $schemeName,
            'module_id'     => (int) $matched->module_id,
            'module_code'   => $moduleCode,
            'module_name'   => $matched->module?->module_name,
            'workflow_type' => $matched->module?->workflow_type ?? 'normal',
        ]);

        return $matched;
    }

    /* LEGACY MANUAL MODULE SELECTION METHODS PRESERVED FOR FUTURE USE:
    protected function loadModulesForScheme($schemeId)
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

        $query = DynamicWorkflowModule::select('id', 'module_name', 'module_code')
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
    */

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
