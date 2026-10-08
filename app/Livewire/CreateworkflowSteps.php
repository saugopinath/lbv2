<?php

namespace App\Livewire;

use App\Models\{Role, Permission};
use App\Models\WorkflowStep;
use App\Models\{User, Codemaster, DynamicWorkflowLabel, DynamicWorkflowModule, DynamicWorkflowSchemeModule, UserRoleSchemeOfficeMapping, workflowstepRolemapping, OfficeMaster, Scheme, District, DynamicWorkflowRequest};
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use App\Attributes\Loggable;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\PermissionRegistrar;

class CreateworkflowSteps extends Component
{
    use WithPagination;
    public $schemeId;
    public $schemeModuleId;
    public $noofSteps;
    public $labels = [];
    public bool $already = false;
    public bool $isEdit = false;
    public bool $originalrolerank = false;
    public $moduleId;
    public $moduleCode;
    public $assignRule = [];
    public $existingRole = [];
    public $newRole = [];
    public $roles = [];
    public $roleSelection = [];
    public $permissionsList = [];
    public $permissionsSelection = [];
    public array $permissionSearch = [];
    public array $copyRoleSelection = [];

    /**
     * Filters permissions list in memory for Mode 2 custom role without executing any database queries.
     */
    public function getFilteredPermissions($index)
    {
        $search = trim($this->permissionSearch[$index] ?? '');
        $collection = collect($this->permissionsList);

        if ($search === '') {
            return $collection;
        }

        return $collection->filter(function ($permission) use ($search) {
            $name = is_array($permission) ? ($permission['name'] ?? '') : ($permission->name ?? '');
            return stripos($name, $search) !== false;
        })->values();
    }

    /**
     * Copies all permissions from an existing selected role and replaces the current step permissions selection.
     */
    public function copyRolePermissions($index)
    {
        $roleId = $this->copyRoleSelection[$index] ?? null;

        if (empty($roleId)) {
            $this->dispatch('toastr', [
                'type' => 'warning',
                'message' => 'Please select a role to copy permissions from.',
            ]);
            return;
        }

        $role = Role::select('id', 'name')->find($roleId);
        if (!$role) {
            $this->dispatch('toastr', [
                'type' => 'error',
                'message' => 'Selected role not found.',
            ]);
            return;
        }

        $permissionIds = DB::table('role_has_permissions')
            ->where('role_id', $roleId)
            ->pluck('permission_id')
            ->toArray();

        $newSelections = [];
        foreach ($permissionIds as $permId) {
            $newSelections[$permId] = true;
        }

        $this->permissionsSelection[$index] = $newSelections;

        $count = count($permissionIds);
        $roleName = $role->name;

        $this->dispatch('toastr', [
            'type' => 'success',
            'message' => "Copied {$count} permission(s) from role '{$roleName}' for Step " . ($index + 1) . ".",
        ]);
    }

    /**
     * Selects all filtered permissions for the specified step.
     */
    public function selectAllFilteredPermissions($index)
    {
        $filtered = $this->getFilteredPermissions($index);
        if (!isset($this->permissionsSelection[$index]) || !is_array($this->permissionsSelection[$index])) {
            $this->permissionsSelection[$index] = [];
        }
        foreach ($filtered as $perm) {
            $id = is_array($perm) ? $perm['id'] : $perm->id;
            $this->permissionsSelection[$index][$id] = true;
        }
    }

    /**
     * Deselects all filtered permissions for the specified step.
     */
    public function deselectAllFilteredPermissions($index)
    {
        $filtered = $this->getFilteredPermissions($index);
        if (isset($this->permissionsSelection[$index]) && is_array($this->permissionsSelection[$index])) {
            foreach ($filtered as $perm) {
                $id = is_array($perm) ? $perm['id'] : $perm->id;
                unset($this->permissionsSelection[$index][$id]);
            }
        }
    }

    /**
     * Clears all selected permissions for the specified step.
     */
    public function clearAllStepPermissions($index)
    {
        $this->permissionsSelection[$index] = [];
    }

    /**
     * Returns roles available for a specific step, excluding roles already selected in previous steps.
     */
    public function getAvailableRolesForStep($index): array
    {
        $usedRoleIds = [];
        for ($i = 0; $i < $index; $i++) {
            if (!empty($this->roleSelection[$i])) {
                foreach ((array) $this->roleSelection[$i] as $roleId) {
                    if (!empty($roleId)) {
                        $usedRoleIds[] = (string) $roleId;
                    }
                }
            }
        }

        if (empty($usedRoleIds)) {
            return $this->roles;
        }

        return array_filter($this->roles, function ($roleName, $roleId) use ($usedRoleIds) {
            return !in_array((string) $roleId, $usedRoleIds, true);
        }, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * Auto-removes duplicated roles from subsequent steps when a previous step selection changes.
     */
    public function updatedRoleSelection($value, $key)
    {
        $stepIndex = (int) $key;
        $selectedInThisStep = (array) ($this->roleSelection[$stepIndex] ?? []);

        if (!empty($selectedInThisStep)) {
            $totalSteps = (int) $this->noofSteps;
            for ($i = $stepIndex + 1; $i < $totalSteps; $i++) {
                if (!empty($this->roleSelection[$i]) && is_array($this->roleSelection[$i])) {
                    $this->roleSelection[$i] = array_values(array_diff($this->roleSelection[$i], $selectedInThisStep));
                }
            }
        }
    }

    // Optional Specific Users Assignment properties
    public $assignSpecificUsers = [];
    public $selectedUserIdsByStep = [];
    public bool $showUserModal = false;
    public ?int $activeStepForUserModal = null;
    public string $modalSearch = '';
    public $modalOfficeType = null; // Preserved for backward compatibility
    public array $modalRoles = [];
    public array $modalOffices = [];
    public array $modalOfficeTypes = [];
    public array $modalSchemes = [];
    public int $modalPageLimit = 5;
    public $officeTypesList = [];
    public array $officesList = [];
    public array $schemesList = [];

    // Step-level Office Mapping properties (Required when user selection > 0)
    public array $stepOfficeType = [];
    public array $stepDistrict = [];
    public array $stepOffice = [];
    public array $districtsList = [];
    public array $stepOfficesList = [];

    // Active Pending Request Guard properties
    public array $stepPendingCounts = [];
    public int $totalPendingRequests = 0;
    public array $existingStepIds = [];

    public function enableEditing()
    {
        $this->isEdit = true;
    }

    public function rendering()
    {
        for ($i = 0; $i < $this->noofSteps; $i++) {
            if (!isset($this->roleSelection[$i]) || !is_array($this->roleSelection[$i])) {
                $this->roleSelection[$i] = [];
            }

            if (!isset($this->permissionsSelection[$i])) {
                $this->permissionsSelection[$i] = [];
            }

            if (!isset($this->permissionSearch[$i])) {
                $this->permissionSearch[$i] = '';
            }

            if (!isset($this->copyRoleSelection[$i])) {
                $this->copyRoleSelection[$i] = '';
            }

            if (!isset($this->assignSpecificUsers[$i])) {
                $this->assignSpecificUsers[$i] = false;
            }

            if (!isset($this->selectedUserIdsByStep[$i]) || !is_array($this->selectedUserIdsByStep[$i])) {
                $this->selectedUserIdsByStep[$i] = [];
            }

            if (!isset($this->stepOfficeType[$i])) {
                $this->stepOfficeType[$i] = null;
            }

            if (!isset($this->stepDistrict[$i])) {
                $this->stepDistrict[$i] = null;
            }

            if (!isset($this->stepOffice[$i])) {
                $this->stepOffice[$i] = null;
            }

            if (!isset($this->stepOfficesList[$i])) {
                $this->stepOfficesList[$i] = [];
            }

            if (!isset($this->stepPendingCounts[$i])) {
                $this->stepPendingCounts[$i] = 0;
            }
        }
    }

    public function mount($schemeData, $moduleData, $isEdit = false)
    {
        $this->isEdit = $isEdit || request()->has('scheme_id');
        $this->schemeId = $schemeData['scheme_id'];
        $this->moduleId = $moduleData['module_id'];
        $this->moduleCode = $moduleData['module_code'];

        /* OLD CODE PRESERVED FOR BACKWARD COMPATIBILITY:
        $this->schemeModuleId = DynamicWorkflowSchemeModule::where('scheme_id', $this->schemeId)
            ->where('module_id', $this->moduleId)
            ->value('id');
        */

        // REQUIREMENT: Enforce DynamicWorkflowSchemeModule creation first so schemeModuleId is always valid
        $schemeModule = DynamicWorkflowSchemeModule::firstOrCreate(
            [
                'scheme_id' => $this->schemeId,
                'module_id' => $this->moduleId,
            ],
            [
                'main_module_code' => $this->moduleCode,
                'is_disabled'      => 0,
            ]
        );
        $this->schemeModuleId = $schemeModule->id;

        $steps = $this->schemeModuleId
            ? DynamicWorkflowLabel::select('id', 'label_name', 'permissions', 'assign_specific_users', 'user_ids')
            ->where('scheme_id', $this->schemeId)
            ->where('module_id', $this->schemeModuleId)
            ->orderBy('id')
            ->get()
            : collect([]);

        // Detect active pending requests for this workflow
        $pendingRequests = DynamicWorkflowRequest::select('current_step_id', 'current_rank', DB::raw('count(*) as total'))
            ->where('module_id', $this->schemeModuleId)
            ->where('scheme_id', $this->schemeId)
            ->groupBy('current_step_id', 'current_rank')
            ->get();

        $this->totalPendingRequests = (int) $pendingRequests->sum('total');

        // FIX: Enforce Original Role Rank Hierarchy for the UI alert by querying whereNotNull('rank')
        $roles = Role::select('id', 'name', 'rank')->whereNotNull('rank')->orderBy('rank')->pluck('name', 'id')->toArray();
        $permissions = Permission::select('name', 'id')->orderBy('name')->get();

        if (!empty($roles)) {
            $this->originalrolerank = true;
            $this->roles = $roles;
        }
        if (!empty($permissions)) {
            $this->permissionsList = $permissions;
        }

        $this->officeTypesList = Codemaster::select('name', 'code')
            ->whereIn('code', [151, 152, 153, 154])
            ->pluck('name', 'code')
            ->toArray();

        $this->officesList = OfficeMaster::select('id', 'name')
            ->where('is_active', 1)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        $this->schemesList = Scheme::select('id', 'name')
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        $this->districtsList = District::select('id', 'name')
            ->orderBy('name', 'asc')
            ->pluck('name', 'id')
            ->toArray();

        if ($steps->isNotEmpty()) {
            $this->noofSteps = $steps->count();
            $this->labels = [];
            $this->already = true;

            $stepIds = $steps->pluck('id')->toArray();
            $allRoleMappings = workflowstepRolemapping::select('workflow_step_id', 'role_id')
                ->whereIn('workflow_step_id', $stepIds)
                ->where('scheme_id', $this->schemeId)
                ->where('module_id', $this->schemeModuleId)
                ->get()
                ->groupBy('workflow_step_id');

            $allSelectedUserIds = $steps->pluck('user_ids')->filter()->flatten()->unique()->toArray();
            $userOfficeMappings = !empty($allSelectedUserIds)
                ? UserRoleSchemeOfficeMapping::with('Office:id,name,office_type_id,district_id')
                ->select('id', 'user_id', 'role_id', 'scheme_id', 'office_id')
                ->where('scheme_id', $this->schemeId)
                ->whereIn('user_id', $allSelectedUserIds)
                ->get()
                ->groupBy('user_id')
                : collect([]);

            // Pre-extract matched office objects for each step outside the loop
            $stepMatchedOffices = [];
            foreach ($steps as $index => $step) {
                $uIds = is_array($step->user_ids) ? $step->user_ids : [];
                foreach ($uIds as $uId) {
                    if (isset($userOfficeMappings[$uId]) && $userOfficeMappings[$uId]->first()?->Office) {
                        $stepMatchedOffices[$index] = $userOfficeMappings[$uId]->first()->Office;
                        break;
                    }
                }
            }

            // Batch fetch all required offices in 1 single query outside the loop
            $officeTypeIds = collect($stepMatchedOffices)->pluck('office_type_id')->filter()->unique()->toArray();
            $allOfficesList = !empty($officeTypeIds)
                ? OfficeMaster::select('id', 'name', 'office_type_id', 'district_id')
                ->whereIn('office_type_id', $officeTypeIds)
                ->where('is_active', 1)
                ->orderBy('name')
                ->get()
                ->groupBy('office_type_id')
                : collect([]);

            foreach ($steps as $index => $step) {
                $this->labels[$index] = $step->label_name;
                $this->assignSpecificUsers[$index] = (bool) $step->assign_specific_users;
                $this->selectedUserIdsByStep[$index] = is_array($step->user_ids) ? array_map('strval', $step->user_ids) : [];
                $this->existingStepIds[$index] = $step->id;
                $this->stepPendingCounts[$index] = (int) $pendingRequests->where('current_step_id', $step->id)->sum('total');

                if (isset($stepMatchedOffices[$index])) {
                    $office = $stepMatchedOffices[$index];
                    $this->stepOfficeType[$index] = $office->office_type_id;
                    $this->stepDistrict[$index] = $office->district_id;
                    $this->stepOffice[$index] = $office->id;

                    $typeOffices = $allOfficesList[$office->office_type_id] ?? collect([]);
                    if ($office->district_id) {
                        $typeOffices = $typeOffices->where('district_id', $office->district_id);
                    }
                    $this->stepOfficesList[$index] = $typeOffices->pluck('name', 'id')->toArray();
                }

                $roleMappings = isset($allRoleMappings[$step->id])
                    ? $allRoleMappings[$step->id]->pluck('role_id')->toArray()
                    : [];

                if (!empty($step->permissions)) {
                    $this->assignRule[$index] = "2";
                    $this->existingRole[$index] = false;
                    $this->newRole[$index] = true;
                    $this->roleSelection[$index] = [];
                    $permSelection = [];
                    $permArray = is_array($step->permissions) ? $step->permissions : json_decode($step->permissions, true);
                    foreach ((array) $permArray as $permId) {
                        $permSelection[$permId] = true;
                    }
                    $this->permissionsSelection[$index] = $permSelection;
                } elseif (!empty($roleMappings)) {
                    $this->assignRule[$index] = "1";
                    $this->existingRole[$index] = true;
                    $this->newRole[$index] = false;
                    $this->roleSelection[$index] = array_map('strval', $roleMappings);
                    $this->permissionsSelection[$index] = [];
                } else {
                    $this->assignRule[$index] = "1";
                    $this->existingRole[$index] = true;
                    $this->newRole[$index] = false;
                    $this->roleSelection[$index] = [];
                    $this->permissionsSelection[$index] = [];
                }
            }
        }
    }
    protected function rules()
    {
        $rules = [
            'noofSteps' => 'required|integer|min:1|max:9',
        ];

        for ($i = 0; $i < $this->noofSteps; $i++) {
            $rules["labels.{$i}"] = 'required';
            $rules["assignRule.{$i}"] = 'required|in:1,2';

            // Validates array and checks each item against roles table
            $rules["roleSelection.{$i}"] = "exclude_unless:assignRule.{$i},1|required|array|min:1";
            $rules["roleSelection.{$i}.*"] = "exclude_unless:assignRule.{$i},1|exists:roles,id";

            $rules["permissionsSelection.{$i}"] = "exclude_unless:assignRule.{$i},2|required|array|min:1";

            if (count(array_filter($this->selectedUserIdsByStep[$i] ?? [])) > 0) {
                $rules["stepOfficeType.{$i}"] = 'required';
                $rules["stepDistrict.{$i}"] = 'required';
                $rules["stepOffice.{$i}"] = 'required';
            }
        }

        return $rules;
    }

    protected function messages()
    {
        $messages = [
            'noofSteps.required' => 'Number of steps is required.',
            'noofSteps.integer' => 'Number of steps must be a number.',
            'noofSteps.min' => 'Number of steps must be at least 1.',
            'noofSteps.max' => 'Number of steps cannot exceed 9.',
        ];

        for ($i = 0; $i < $this->noofSteps; $i++) {
            $stepNum = $i + 1;

            $messages["labels.{$i}.required"] = "Label Name is required for Step {$stepNum}.";

            $messages["assignRule.{$i}.required"] = "Assign rule is required for Step {$stepNum}.";
            $messages["assignRule.{$i}.in"] = "Invalid assign rule selected for Step {$stepNum}.";

            $messages["roleSelection.{$i}.required"] = "Role selection is required for Step {$stepNum}.";
            $messages["roleSelection.{$i}.min"] = "Please select at least one role for Step {$stepNum}.";
            $messages["roleSelection.{$i}.*.exists"] = "Selected role for Step {$stepNum} is invalid.";

            $messages["permissionsSelection.{$i}.required"] = "Please select at least one permission for Step {$stepNum}.";
            $messages["permissionsSelection.{$i}.min"] = "Please select at least one permission for Step {$stepNum}.";

            $messages["stepOfficeType.{$i}.required"] = "Office Type is required for Step {$stepNum} when users are selected.";
            $messages["stepDistrict.{$i}.required"] = "District is required for Step {$stepNum} when users are selected.";
            $messages["stepOffice.{$i}.required"] = "Office is required for Step {$stepNum} when users are selected.";
        }

        return $messages;
    }

    public function updatedNoofSteps($value)
    {
        $value = (int) $value;
        if ($value > 9) {
            $this->noofSteps = 9;
            $value = 9;
        }
        if ($value < 1) {
            $this->labels = [];
            return;
        }

        // GUARD: Prevent reducing steps if removed steps hold active pending requests
        $existingCount = count($this->labels);
        if ($value < $existingCount) {
            for ($checkIdx = $value; $checkIdx < $existingCount; $checkIdx++) {
                if (!empty($this->stepPendingCounts[$checkIdx]) && $this->stepPendingCounts[$checkIdx] > 0) {
                    $this->noofSteps = $existingCount;
                    $this->dispatch('toastr', [
                        'type' => 'error',
                        'message' => "Cannot reduce steps to {$value}. Step " . ($checkIdx + 1) . " has {$this->stepPendingCounts[$checkIdx]} active pending request(s)."
                    ]);
                    return;
                }
            }
        }
        $existingLabels = $this->labels;
        $existingRoleSel = $this->roleSelection;
        $existingPermSel = $this->permissionsSelection;
        $existingPermSearch = $this->permissionSearch;
        $existingCopyRoleSel = $this->copyRoleSelection;
        $existingAssignRule = $this->assignRule;
        $existingExistingRole = $this->existingRole;
        $existingNewRole = $this->newRole;
        $existingAssignSpecificUsers = $this->assignSpecificUsers;
        $existingSelectedUserIds = $this->selectedUserIdsByStep;
        $existingStepOfficeType = $this->stepOfficeType;
        $existingStepDistrict = $this->stepDistrict;
        $existingStepOffice = $this->stepOffice;
        $existingStepOfficesList = $this->stepOfficesList;

        $this->labels = [];
        $this->roleSelection = [];
        $this->permissionsSelection = [];
        $this->permissionSearch = [];
        $this->copyRoleSelection = [];
        $this->assignRule = [];
        $this->existingRole = [];
        $this->newRole = [];
        $this->assignSpecificUsers = [];
        $this->selectedUserIdsByStep = [];
        $this->stepOfficeType = [];
        $this->stepDistrict = [];
        $this->stepOffice = [];
        $this->stepOfficesList = [];

        for ($i = 0; $i < $value; $i++) {
            $this->labels[$i] = $existingLabels[$i] ?? '';
            $this->permissionSearch[$i] = $existingPermSearch[$i] ?? '';
            $this->copyRoleSelection[$i] = $existingCopyRoleSel[$i] ?? '';
            $this->assignRule[$i] = $existingAssignRule[$i] ?? "1";
            $this->existingRole[$i] = $existingExistingRole[$i] ?? true;
            $this->newRole[$i] = $existingNewRole[$i] ?? false;
            $this->assignSpecificUsers[$i] = $existingAssignSpecificUsers[$i] ?? false;
            $this->selectedUserIdsByStep[$i] = $existingSelectedUserIds[$i] ?? [];
            $this->stepOfficeType[$i] = $existingStepOfficeType[$i] ?? null;
            $this->stepDistrict[$i] = $existingStepDistrict[$i] ?? null;
            $this->stepOffice[$i] = $existingStepOffice[$i] ?? null;
            $this->stepOfficesList[$i] = $existingStepOfficesList[$i] ?? [];

            if (isset($existingRoleSel[$i])) {
                $this->roleSelection[$i] = $existingRoleSel[$i];
            }
            if (isset($existingPermSel[$i])) {
                $this->permissionsSelection[$i] = $existingPermSel[$i];
            }
        }
    }
    public function updatedassignRule($v)
    {
        if (!empty($this->assignRule)) {
            foreach ($this->assignRule as $index => $ruleValue) {
                if ($ruleValue == "1") {
                    $this->existingRole[$index] = true;
                    $this->newRole[$index] = false;
                    unset($this->permissionsSelection[$index]);
                } elseif ($ruleValue == "2") {
                    $this->existingRole[$index] = false;
                    $this->newRole[$index] = true;
                    unset($this->roleSelection[$index]);
                }
            }
        }
    }

    // Modal Action Methods
    public function openUserModal($index)
    {
        $this->activeStepForUserModal = (int) $index;
        if (!isset($this->selectedUserIdsByStep[$index]) || !is_array($this->selectedUserIdsByStep[$index])) {
            $this->selectedUserIdsByStep[$index] = [];
        }
        $this->modalSearch = '';
        $this->modalOfficeType = null;
        $this->modalOffices = [];
        $this->modalOfficeTypes = [];
        $this->modalSchemes = [];

        // QOL Feature: Auto-apply selected roles from Step configuration if Mode 1 (Select from Existing) is used
        $rule = $this->assignRule[$index] ?? "1";
        if ($rule == "1" && !empty($this->roleSelection[$index])) {
            $this->modalRoles = array_map('strval', (array) $this->roleSelection[$index]);
        } else {
            $this->modalRoles = [];
        }

        $this->resetPage('modalPage');
        $this->showUserModal = true;
    }

    public function closeUserModal()
    {
        $this->showUserModal = false;
        $this->activeStepForUserModal = null;
    }

    public function updatedModalSearch()
    {
        $this->resetPage('modalPage');
    }

    public function updatedModalOfficeType()
    {
        $this->resetPage('modalPage');
    }

    public function updatedModalRoles()
    {
        $this->resetPage('modalPage');
    }

    public function updatedModalOffices()
    {
        $this->resetPage('modalPage');
    }

    public function updatedModalOfficeTypes()
    {
        $this->resetPage('modalPage');
    }

    public function updatedModalSchemes()
    {
        $this->resetPage('modalPage');
    }

    public function updatedModalPageLimit()
    {
        $this->resetPage('modalPage');
    }

    public function resetModalFilters()
    {
        $this->modalSearch = '';
        $this->modalOfficeType = null;
        $this->modalRoles = [];
        $this->modalOffices = [];
        $this->modalOfficeTypes = [];
        $this->modalSchemes = [];
        $this->resetPage('modalPage');
    }

    public function toggleSelectUser($userId)
    {
        $stepIndex = $this->activeStepForUserModal;
        if ($stepIndex === null) return;

        $userId = (string) $userId;
        if (!isset($this->selectedUserIdsByStep[$stepIndex]) || !is_array($this->selectedUserIdsByStep[$stepIndex])) {
            $this->selectedUserIdsByStep[$stepIndex] = [];
        }

        $currentSelected = array_map('strval', $this->selectedUserIdsByStep[$stepIndex]);

        if (in_array($userId, $currentSelected, true)) {
            $this->selectedUserIdsByStep[$stepIndex] = array_values(array_diff($currentSelected, [$userId]));
        } else {
            $this->selectedUserIdsByStep[$stepIndex] = array_values(array_unique(array_merge($currentSelected, [$userId])));
        }
    }

    public function toggleSelectAllVisible(array $visibleUserIds)
    {
        $stepIndex = $this->activeStepForUserModal;
        if ($stepIndex === null) return;

        $visibleUserIds = array_map('strval', $visibleUserIds);
        if (!isset($this->selectedUserIdsByStep[$stepIndex]) || !is_array($this->selectedUserIdsByStep[$stepIndex])) {
            $this->selectedUserIdsByStep[$stepIndex] = [];
        }

        $currentSelected = array_map('strval', $this->selectedUserIdsByStep[$stepIndex]);

        $allSelected = count($visibleUserIds) > 0;
        foreach ($visibleUserIds as $uId) {
            if (!in_array($uId, $currentSelected, true)) {
                $allSelected = false;
                break;
            }
        }

        if ($allSelected) {
            $this->selectedUserIdsByStep[$stepIndex] = array_values(array_diff($currentSelected, $visibleUserIds));
        } else {
            $this->selectedUserIdsByStep[$stepIndex] = array_values(array_unique(array_merge($currentSelected, $visibleUserIds)));
        }
    }

    /**
     * DRY Query Builder helper for filtering users in Modal efficiently (Optimized for Lakhs of users)
     */
    protected function buildModalUsersQuery()
    {
        $query = User::query()->select(['id', 'name', 'email', 'mobile_no'])->where('is_active', 1);

        // 1. Search filter (Name, Email, Mobile)
        if (!empty($this->modalSearch)) {
            $search = trim($this->modalSearch);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('mobile_no', 'like', "%{$search}%");
            });
        }

        // 2. Multi-Role filter
        $selectedRoles = array_filter((array) $this->modalRoles);
        if (!empty($selectedRoles)) {
            $query->where(function ($q) use ($selectedRoles) {
                $q->whereHas('RoleSchemeOfficeMappings', function ($sub) use ($selectedRoles) {
                    $sub->whereIn('role_id', $selectedRoles);
                })->orWhereHas('roles', function ($sub) use ($selectedRoles) {
                    $sub->whereIn('roles.id', $selectedRoles);
                });
            });
        }

        // 3. Multi-Office filter
        $selectedOffices = array_filter((array) $this->modalOffices);
        if (!empty($selectedOffices)) {
            $query->whereHas('RoleSchemeOfficeMappings', function ($sub) use ($selectedOffices) {
                $sub->whereIn('office_id', $selectedOffices);
            });
        }

        // 4. Multi-Office Type filter (supports new multi-select array + backward compatible single modalOfficeType)
        $selectedOfficeTypes = array_filter((array) $this->modalOfficeTypes);
        if (!empty($this->modalOfficeType)) {
            $selectedOfficeTypes[] = $this->modalOfficeType;
        }
        $selectedOfficeTypes = array_unique(array_filter($selectedOfficeTypes));

        if (!empty($selectedOfficeTypes)) {
            $query->whereHas('RoleSchemeOfficeMappings.office', function ($sub) use ($selectedOfficeTypes) {
                $sub->whereIn('office_type_id', $selectedOfficeTypes);
            });
        }

        // 5. Multi-Scheme filter
        $selectedSchemes = array_filter((array) $this->modalSchemes);
        if (!empty($selectedSchemes)) {
            $query->whereHas('RoleSchemeOfficeMappings', function ($sub) use ($selectedSchemes) {
                $sub->whereIn('scheme_id', $selectedSchemes);
            });
        }

        return $query;
    }

    public function selectAllFilteredUsers()
    {
        $stepIndex = $this->activeStepForUserModal;
        if ($stepIndex === null) return;

        $query = $this->buildModalUsersQuery();
        $filteredIds = array_map('strval', $query->pluck('id')->toArray());

        if (!isset($this->selectedUserIdsByStep[$stepIndex]) || !is_array($this->selectedUserIdsByStep[$stepIndex])) {
            $this->selectedUserIdsByStep[$stepIndex] = [];
        }

        $this->selectedUserIdsByStep[$stepIndex] = array_values(array_unique(array_merge($this->selectedUserIdsByStep[$stepIndex], $filteredIds)));
    }

    public function clearStepUserSelection()
    {
        $stepIndex = $this->activeStepForUserModal;
        if ($stepIndex === null) return;

        $this->selectedUserIdsByStep[$stepIndex] = [];
    }

    public function getModalUsersProperty()
    {
        $stepIndex = $this->activeStepForUserModal;
        if ($stepIndex === null) return null;

        /* Preserved old NO_ROLE_SELECTED logic as comment for backward compatibility:
        $rule = $this->assignRule[$stepIndex] ?? "1";
        if ($rule == "1") {
            $selectedRoleIds = array_filter((array) ($this->roleSelection[$stepIndex] ?? []));
            if (empty($selectedRoleIds)) {
                return 'NO_ROLE_SELECTED';
            }
        }
        */

        $query = $this->buildModalUsersQuery();
        return $query->orderBy('name', 'asc')->paginate((int) $this->modalPageLimit, ['*'], 'modalPage');
    }

    public function updatedStepOfficeType($value, $key)
    {
        $stepIndex = (int) $key;
        $this->stepDistrict[$stepIndex] = null;
        $this->stepOffice[$stepIndex] = null;
        $this->stepOfficesList[$stepIndex] = [];

        if ($value) {
            $query = OfficeMaster::select('id', 'name')->where('office_type_id', $value)->where('is_active', 1);
            if (!empty($this->stepDistrict[$stepIndex])) {
                $query->where('district_id', $this->stepDistrict[$stepIndex]);
            }
            $this->stepOfficesList[$stepIndex] = $query->orderBy('name')->pluck('name', 'id')->toArray();
        }
    }

    public function updatedStepDistrict($value, $key)
    {
        $stepIndex = (int) $key;
        $this->stepOffice[$stepIndex] = null;
        $officeType = $this->stepOfficeType[$stepIndex] ?? null;

        if ($officeType) {
            $query = OfficeMaster::select('id', 'name')->where('office_type_id', $officeType)->where('is_active', 1);
            if (!empty($value)) {
                $query->where('district_id', $value);
            }
            $this->stepOfficesList[$stepIndex] = $query->orderBy('name')->pluck('name', 'id')->toArray();
        }
    }

    #[Loggable(level: 'C', nickname: 'Create Workflow Steps')]
    public function save()
    {
        for ($i = 0; $i < $this->noofSteps; $i++) {
            $rule = $this->assignRule[$i] ?? null;

            if ($rule == 1) {
                // Clear permissions if Role is chosen
                $this->permissionsSelection[$i] = [];
            } elseif ($rule == 2) {
                // Clear role selection if Custom Permissions is chosen
                $this->roleSelection[$i] = [];

                // Clean out boolean false values from unchecked checkboxes
                if (isset($this->permissionsSelection[$i]) && is_array($this->permissionsSelection[$i])) {
                    $this->permissionsSelection[$i] = array_filter($this->permissionsSelection[$i]);
                } else {
                    $this->permissionsSelection[$i] = [];
                }
            }
        }
        $this->validate();

        // Validate step office mapping if users are selected
        for ($i = 0; $i < $this->noofSteps; $i++) {
            $selectedUsersCount = count(array_filter($this->selectedUserIdsByStep[$i] ?? []));
            if ($selectedUsersCount > 0) {
                if (empty($this->stepOfficeType[$i])) {
                    $this->addError("stepOfficeType.{$i}", "Office Type is required for Step " . ($i + 1) . " when users are selected.");
                }
                if (empty($this->stepDistrict[$i])) {
                    $this->addError("stepDistrict.{$i}", "District is required for Step " . ($i + 1) . " when users are selected.");
                }
                if (empty($this->stepOffice[$i])) {
                    $this->addError("stepOffice.{$i}", "Office is required for Step " . ($i + 1) . " when users are selected.");
                }
            }
        }

        if ($this->getErrorBag()->any()) {
            $this->dispatch('toastr', [
                'type' => 'error',
                'message' => 'Please fill in required Office Type, District, and Office for steps with selected users.'
            ]);
            return;
        }

        if (!Auth::check()) {
            session()->flash('error', 'Authentication session expired. Please login again.');
            return;
        }

        try {
            DB::beginTransaction();

            $schemeId = $this->schemeId;
            $moduleId = $this->moduleId;
            $moduleCode = $this->moduleCode;
            $stepCount = (int) $this->noofSteps;
            $roleSelection = array_filter($this->roleSelection ?? []);
            $permissionsSelection = array_filter($this->permissionsSelection ?? [], fn($item) => !empty($item));

            $schemeModule = DynamicWorkflowSchemeModule::updateOrCreate(
                [
                    'scheme_id' => $schemeId,
                    'module_id' => $moduleId,
                ],
                [
                    'scheme_id'        => $schemeId,
                    'module_id'        => $moduleId,
                    'main_module_code' => $moduleCode,
                    'step_count'       => $stepCount,
                ]
            );

            /* OLD CODE PRESERVED FOR BACKWARD COMPATIBILITY:
            workflowstepRolemapping::where('module_id', $schemeModule->id)->where('scheme_id', $schemeId)->delete();
            DynamicWorkflowLabel::where('module_id', $schemeModule->id)->where('scheme_id', $schemeId)->delete();
            */

            // Retrieve existing step labels to preserve IDs and avoid breaking in-flight pending requests
            $existingLabels = DynamicWorkflowLabel::select('id', 'scheme_id', 'module_id', 'label_name', 'op_type_id', 'assign_specific_users', 'user_ids', 'permissions')
                ->where('module_id', $schemeModule->id)
                ->where('scheme_id', $schemeId)
                ->orderBy('id')
                ->get();

            $savedLabelIds = [];

            $parent = Codemaster::select('id', 'code')->where('short_name', 'dynamic_op_type')->first();
            $maxCode = null;
            $parent_id = null;
            $parent_code = null;

            if ($parent) {
                $parent_id = $parent->id;
                $parent_code = $parent->code;
                $maxCode = Codemaster::where('parent_short_code', 'dynamic_op_type')->max('code');
            }

            // Clean up role mappings before re-inserting fresh mappings
            workflowstepRolemapping::where('module_id', $schemeModule->id)->where('scheme_id', $schemeId)->delete();

            for ($i = 0; $i < $stepCount; $i++) {
                $rank = ($i + 1) * 10;
                $successRank = ($i < $stepCount - 1) ? ($i + 2) * 10 : 0;
                $revertRank = ($i > 0) ? $i * 10 : - ($schemeId);
                $opTypeId = null;

                if ($parent) {
                    $labelSlug = strtolower(str_replace(' ', '_', $this->labels[$i]));
                    $codemaster = Codemaster::select('id')
                        ->where('parent_id', $parent_id)
                        ->where('short_name', strtolower($moduleCode) . '_' . $labelSlug)
                        ->first();

                    if (!$codemaster) {
                        if (!$maxCode) {
                            $maxCode = ($parent_code * 10);
                        }
                        $maxCode++;

                        $codemaster = Codemaster::create([
                            'name'              => strtoupper($this->labels[$i]),
                            'short_name'        => strtolower($moduleCode) . '_' . $labelSlug,
                            'parent_id'         => $parent_id,
                            'parent_short_code' => 'dynamic_op_type',
                            'code'              => $maxCode,
                            'is_active'         => 1,
                        ]);
                    }
                    $opTypeId = $codemaster->id;
                }

                $isSpecificUserMode = !empty($this->assignSpecificUsers[$i]);
                $selectedUserIds = isset($this->selectedUserIdsByStep[$i]) && is_array($this->selectedUserIdsByStep[$i])
                    ? array_values(array_unique(array_filter($this->selectedUserIdsByStep[$i])))
                    : [];

                $existingLabel = $existingLabels->get($i);

                if ($existingLabel) {
                    $existingLabel->update([
                        'label_name'            => $this->labels[$i],
                        'op_type_id'            => $opTypeId,
                        'assign_specific_users' => $isSpecificUserMode,
                        'user_ids'              => $isSpecificUserMode ? $selectedUserIds : null,
                    ]);
                    $label = $existingLabel;
                } else {
                    $label = DynamicWorkflowLabel::create([
                        'scheme_id'             => $schemeId,
                        'module_id'             => $schemeModule->id,
                        'label_name'            => $this->labels[$i],
                        'op_type_id'            => $opTypeId,
                        'assign_specific_users' => $isSpecificUserMode,
                        'user_ids'              => $isSpecificUserMode ? $selectedUserIds : null,
                    ]);
                }
                $savedLabelIds[] = $label->id;

                $assignedRoleIds = [];

                // FEATURE: Auto-create the module access permission
                $moduleAccessPerm = strtolower(str_replace(' ', '_', $moduleCode)) . '_access';
                $permModel = Permission::firstOrCreate(
                    ['name' => $moduleAccessPerm, 'guard_name' => 'web'],
                    ['description' => "Grants role access to view and execute actions in the '{$moduleCode}' dynamic workflow module."]
                );

                // MODE 1: Existing Role Selection
                if (!empty($roleSelection[$i])) {
                    $assignedRoleIds = (array) $roleSelection[$i];
                    $label->update(['permissions' => null]);
                }

                // MODE 2: Custom Role Creation via Permissions
                if (!empty($permissionsSelection[$i])) {
                    $selectedPermissionIds = array_keys(array_filter($permissionsSelection[$i]));

                    if (!empty($selectedPermissionIds)) {
                        // FIX: Postpend scheme and module code to make role names unique across workflows while keeping label search-friendly
                        $uniqueRoleName = ucfirst($this->labels[$i]) . '_' . strtoupper($moduleCode) . '_' . $schemeId;
                        $crRole = Role::firstOrCreate(
                            [
                                'name'       => $uniqueRoleName,
                                'guard_name' => 'web',
                            ],
                            [
                                'rank'       => (Role::max('rank') ?? 0) + 1,
                            ]
                        );

                        $permissions = Permission::select('id', 'name', 'guard_name')->whereIn('id', $selectedPermissionIds)->get();

                        // Sync revokes if updating
                        $currentPermissions = $crRole->permissions->pluck('id')->toArray();
                        if (!empty($currentPermissions)) {
                            $permissionsToRevoke = array_diff($currentPermissions, $selectedPermissionIds);
                            foreach ($permissionsToRevoke as $permId) {
                                $crRole->revokePermissionTo($permId);
                            }
                        }

                        foreach ($permissions as $permission) {
                            if (!$crRole->hasPermissionTo($permission->name)) {
                                $crRole->givePermissionTo($permission);
                            }
                        }

                        // Ensure custom role always has module access permission
                        if (!$crRole->hasPermissionTo($moduleAccessPerm)) {
                            $crRole->givePermissionTo($moduleAccessPerm);
                        }

                        // Add newly created role ID so workflow step mapping can record it
                        $assignedRoleIds[] = $crRole->id;

                        // Save permission IDs to label
                        $label->update(['permissions' => $selectedPermissionIds]);
                    }
                }

                // Global Mode: Always ensure all assigned roles have the module access permission
                if (!empty($assignedRoleIds)) {
                    $roles = Role::select('id', 'name', 'guard_name')->whereIn('id', $assignedRoleIds)->get();
                    foreach ($roles as $role) {
                        if (!$role->hasPermissionTo($moduleAccessPerm)) {
                            $role->givePermissionTo($moduleAccessPerm);
                        }
                    }
                }

                // Specific User Mode: Assign module access permission and custom roles to selected users
                if ($isSpecificUserMode && !empty($selectedUserIds)) {
                    $users = User::select('id')->whereIn('id', $selectedUserIds)->get();
                    app(PermissionRegistrar::class)->setPermissionsTeamId($schemeId);

                    foreach ($users as $user) {
                        // Direct module permission attachment under the scheme
                        $user->givePermissionWithScheme($permModel->id, $schemeId);

                        // If a custom role was created for this step, also assign the role under this scheme team
                        if (!empty($permissionsSelection[$i]) && isset($crRole)) {
                            if (!$user->hasRole($crRole)) {
                                $user->assignRole($crRole);
                            }
                        }
                    }
                }

                // Save Workflow Step Role Mappings for whichever mode was used
                if (!empty($assignedRoleIds)) {
                    $mappingData = [];
                    foreach ($assignedRoleIds as $roleId) {
                        $mappingData[] = [
                            'scheme_id'          => $schemeId,
                            'module_id'          => $schemeModule->id,
                            'workflow_step_id'   => $label->id,
                            'role_id'            => $roleId,
                            'rank'               => $rank,
                            'next_level_role_id' => $successRank,
                            'same_level_role_id' => $revertRank,
                            'is_final_step'      => ($i == $stepCount - 1),
                            'action_type'        => null,
                            'created_at'         => now(),
                            'updated_at'         => now(),
                        ];
                    }
                    workflowstepRolemapping::insert($mappingData);
                }

                // Save UserRoleSchemeOfficeMapping records in bulk if users are selected for step $i
                if (!empty($selectedUserIds) && !empty($assignedRoleIds) && !empty($this->stepOffice[$i])) {
                    $officeId = $this->stepOffice[$i];

                    // Batch query existing records for performance optimization
                    $existingMappings = UserRoleSchemeOfficeMapping::select('id', 'scheme_id', 'user_id', 'role_id', 'office_id')
                        ->where('scheme_id', $schemeId)
                        ->whereIn('user_id', $selectedUserIds)
                        ->whereIn('role_id', $assignedRoleIds)
                        ->where('office_id', $officeId)
                        ->get()
                        ->groupBy(fn($item) => $item->user_id . '_' . $item->role_id . '_' . $item->office_id);

                    $now = now();
                    $insertData = [];

                    foreach ($selectedUserIds as $uId) {
                        foreach ($assignedRoleIds as $rId) {
                            $key = $uId . '_' . $rId . '_' . $officeId;
                            if (!isset($existingMappings[$key])) {
                                $insertData[] = [
                                    'user_id'    => $uId,
                                    'scheme_id'  => $schemeId,
                                    'role_id'    => $rId,
                                    'office_id'  => $officeId,
                                    'created_at' => $now,
                                    'updated_at' => $now,
                                ];
                            }
                        }
                    }

                    // Perform chunked bulk insertion for high performance (Lakhs of users)
                    if (!empty($insertData)) {
                        foreach (array_chunk($insertData, 500) as $chunk) {
                            UserRoleSchemeOfficeMapping::insert($chunk);
                        }
                    }

                    // Sync Spatie team roles for selected users under the current scheme team
                    app(PermissionRegistrar::class)->setPermissionsTeamId($schemeId);
                    $usersToAssign = User::select('id')->whereIn('id', $selectedUserIds)->get();
                    $rolesToAssign = Role::select('id', 'name', 'guard_name')->whereIn('id', $assignedRoleIds)->get();

                    foreach ($usersToAssign as $u) {
                        foreach ($rolesToAssign as $r) {
                            if (!$u->hasRole($r)) {
                                $u->assignRole($r);
                            }
                        }
                    }
                }
            }

            // Clean up any old labels that were safely removed (not present in savedLabelIds)
            $labelsToDelete = $existingLabels->whereNotIn('id', $savedLabelIds);
            foreach ($labelsToDelete as $delLabel) {
                workflowstepRolemapping::where('workflow_step_id', $delLabel->id)->delete();
                $delLabel->delete();
            }

            app(PermissionRegistrar::class)->forgetCachedPermissions();
            DB::commit();

            $this->already = true;
            $this->dispatch('toastr', [
                'type'    => 'success',
                'message' => 'Workflow steps saved successfully!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            $this->already = false;
            $this->dispatch('toastr', [
                'type'    => 'error',
                'message' => 'Something went wrong. Please try again.' . $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.createworkflow-steps');
    }
}
