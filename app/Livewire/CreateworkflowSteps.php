<?php

namespace App\Livewire;

use App\Models\{Role, Permission};
use App\Models\WorkflowStep;
use App\Models\{User, Codemaster, DynamicWorkflowLabel, DynamicWorkflowModule, DynamicWorkflowSchemeModule, UserRoleSchemeOfficeMapping, workflowstepRolemapping};
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

    // Optional Specific Users Assignment properties
    public $assignSpecificUsers = [];
    public $selectedUserIdsByStep = [];
    public bool $showUserModal = false;
    public ?int $activeStepForUserModal = null;
    public string $modalSearch = '';
    public $modalOfficeType = null;
    public int $modalPageLimit = 5;
    public $officeTypesList = [];

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

            if (!isset($this->assignSpecificUsers[$i])) {
                $this->assignSpecificUsers[$i] = false;
            }

            if (!isset($this->selectedUserIdsByStep[$i]) || !is_array($this->selectedUserIdsByStep[$i])) {
                $this->selectedUserIdsByStep[$i] = [];
            }
        }
    }

    public function mount($schemeData, $moduleData, $isEdit = false)
    {
        $this->isEdit = $isEdit;
        $this->schemeId = $schemeData['scheme_id'];
        $this->moduleId = $moduleData['module_id'];
        $this->moduleCode = $moduleData['module_code'];

        $this->schemeModuleId = DynamicWorkflowSchemeModule::where('scheme_id', $this->schemeId)
            ->where('module_id', $this->moduleId)
            ->value('id');

        $steps = $this->schemeModuleId
            ? DynamicWorkflowLabel::select('id', 'label_name', 'permissions', 'assign_specific_users', 'user_ids')
            ->where('scheme_id', $this->schemeId)
            ->where('module_id', $this->schemeModuleId)
            ->orderBy('id')
            ->get()
            : collect([]);

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

        $this->officeTypesList = Codemaster::select('code', 'name')
            ->whereIn('code', [151, 152, 153, 154])
            ->get()
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

            foreach ($steps as $index => $step) {
                $this->labels[$index] = $step->label_name;
                $this->assignSpecificUsers[$index] = (bool) $step->assign_specific_users;
                $this->selectedUserIdsByStep[$index] = is_array($step->user_ids) ? array_map('strval', $step->user_ids) : [];

                $roleMappings = isset($allRoleMappings[$step->id])
                    ? $allRoleMappings[$step->id]->pluck('role_id')->toArray()
                    : [];

                if (!empty($roleMappings)) {
                    $this->assignRule[$index] = "1";
                    $this->existingRole[$index] = true;
                    $this->newRole[$index] = false;
                    $this->roleSelection[$index] = array_map('strval', $roleMappings);
                    $this->permissionsSelection[$index] = [];
                } elseif (!empty($step->permissions)) {
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
        $existingLabels = $this->labels;
        $existingRoleSel = $this->roleSelection;
        $existingPermSel = $this->permissionsSelection;
        $existingAssignRule = $this->assignRule;
        $existingExistingRole = $this->existingRole;
        $existingNewRole = $this->newRole;
        $existingAssignSpecificUsers = $this->assignSpecificUsers;
        $existingSelectedUserIds = $this->selectedUserIdsByStep;

        $this->labels = [];
        $this->roleSelection = [];
        $this->permissionsSelection = [];
        $this->assignRule = [];
        $this->existingRole = [];
        $this->newRole = [];
        $this->assignSpecificUsers = [];
        $this->selectedUserIdsByStep = [];

        for ($i = 0; $i < $value; $i++) {
            $this->labels[$i] = $existingLabels[$i] ?? '';
            $this->assignRule[$i] = $existingAssignRule[$i] ?? "1";
            $this->existingRole[$i] = $existingExistingRole[$i] ?? true;
            $this->newRole[$i] = $existingNewRole[$i] ?? false;
            $this->assignSpecificUsers[$i] = $existingAssignSpecificUsers[$i] ?? false;
            $this->selectedUserIdsByStep[$i] = $existingSelectedUserIds[$i] ?? [];

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

    public function updatedModalPageLimit()
    {
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


    public function selectAllFilteredUsers()
    {
        $stepIndex = $this->activeStepForUserModal;
        if ($stepIndex === null) return;

        $rule = $this->assignRule[$stepIndex] ?? "1";
        $query = User::query()->where('is_active', 1);

        if ($rule == "1") {
            $selectedRoleIds = array_filter((array) ($this->roleSelection[$stepIndex] ?? []));
            if (empty($selectedRoleIds)) {
                return;
            }

            $query->where(function ($q) use ($selectedRoleIds) {
                $q->whereHas('RoleSchemeOfficeMappings', function ($sub) use ($selectedRoleIds) {
                    $sub->whereIn('role_id', $selectedRoleIds);
                })->orWhereHas('roles', function ($sub) use ($selectedRoleIds) {
                    $sub->whereIn('roles.id', $selectedRoleIds);
                });
            });
        }

        if (!empty($this->modalSearch)) {
            $search = $this->modalSearch;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('mobile_no', 'like', "%{$search}%");
            });
        }

        if (!empty($this->modalOfficeType)) {
            $officeType = $this->modalOfficeType;
            $query->whereHas('RoleSchemeOfficeMappings.office', function ($q) use ($officeType) {
                $q->where('office_type_id', $officeType);
            });
        }

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

        $rule = $this->assignRule[$stepIndex] ?? "1";
        $query = User::query()->where('is_active', 1);

        if ($rule == "1") {
            $selectedRoleIds = array_filter((array) ($this->roleSelection[$stepIndex] ?? []));
            if (empty($selectedRoleIds)) {
                return 'NO_ROLE_SELECTED';
            }

            $query->where(function ($q) use ($selectedRoleIds) {
                $q->whereHas('RoleSchemeOfficeMappings', function ($sub) use ($selectedRoleIds) {
                    $sub->whereIn('role_id', $selectedRoleIds);
                })->orWhereHas('roles', function ($sub) use ($selectedRoleIds) {
                    $sub->whereIn('roles.id', $selectedRoleIds);
                });
            });
        }

        if (!empty($this->modalSearch)) {
            $search = $this->modalSearch;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('mobile_no', 'like', "%{$search}%");
            });
        }

        if (!empty($this->modalOfficeType)) {
            $officeType = $this->modalOfficeType;
            $query->whereHas('RoleSchemeOfficeMappings.office', function ($q) use ($officeType) {
                $q->where('office_type_id', $officeType);
            });
        }

        $query->with([
            'RoleSchemeOfficeMappings.office.officeType',
            'RoleSchemeOfficeMappings.Role',
            'mappedRoles',
            'roles'
        ])->orderBy('name', 'asc');

        return $query->paginate((int) $this->modalPageLimit, ['*'], 'modalPage');
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

        if (!Auth::check()) {
            session()->flash('error', 'Authentication session expired. Please login again.');
            return;
        }

        // DB::beginTransaction();
        // try {
        //     $parentId = null;
        //     $totalSteps = count($this->labels);
        //     $data = [];
        //     for ($i = 0; $i < $totalSteps; $i++) {
        //         $data[] = [
        //             'scheme_id' => $this->schemeId,
        //             'rank' => $i,
        //             'label' => $this->labels[$i],
        //             'parent_id' => $parentId,
        //             'is_first' => ($i === 0),
        //             'is_last' => ($i === $totalSteps - 1),
        //             'created_at' => now(),
        //             'updated_at' => now(),
        //         ];
        //         // $step = new WorkflowStep();
        //         // $step->scheme_id = $this->schemeId;
        //         // $step->rank = $i + 1;
        //         // $step->label = $this->labels[$i];
        //         // $step->parent_id = $parentId;
        //         // $step->is_first = ($i === 0);
        //         // $step->is_last = ($i === $totalSteps - 1);
        //         // $step->save();
        //         // $parentId = $step->id;
        //     }
        //     WorkflowStep::insert($data);
        //     DB::commit();
        //     $this->already = true;
        //     $this->dispatch('toastr', [
        //         'type' => 'success',
        //         'message' => 'Workflow steps created successfully!'
        //     ]);
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     $this->already = false;
        //     $this->dispatch('toastr', [
        //         'type' => 'error',
        //         'message' => 'Something went wrong. Please try again.'
        //     ]);
        // }

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

            // Clean up old records for update capability
            workflowstepRolemapping::where('module_id', $schemeModule->id)->where('scheme_id', $schemeId)->delete();
            DynamicWorkflowLabel::where('module_id', $schemeModule->id)->where('scheme_id', $schemeId)->delete();

            $parent = Codemaster::select('id', 'code')->where('short_name', 'dynamic_op_type')->first();
            $maxCode = null;
            $parent_id = null;
            $parent_code = null;

            if ($parent) {
                $parent_id = $parent->id;
                $parent_code = $parent->code;
                $maxCode = Codemaster::where('parent_short_code', 'dynamic_op_type')->max('code');
            }

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

                $label = DynamicWorkflowLabel::create([
                    'scheme_id'             => $schemeId,
                    'module_id'             => $schemeModule->id,
                    'label_name'            => $this->labels[$i],
                    'op_type_id'            => $opTypeId,
                    'assign_specific_users' => $isSpecificUserMode,
                    'user_ids'              => $isSpecificUserMode ? $selectedUserIds : null,
                ]);

                $assignedRoleIds = [];

                // FEATURE: Auto-create the module access permission
                $moduleAccessPerm = strtolower(str_replace(' ', '_', $moduleCode)) . '_access';
                $permModel = Permission::firstOrCreate(['name' => $moduleAccessPerm, 'guard_name' => 'web']);

                // MODE 1: Existing Role Selection
                if (!empty($roleSelection[$i])) {
                    $assignedRoleIds = (array) $roleSelection[$i];
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

                        $permissions = Permission::select('id', 'name')->whereIn('id', $selectedPermissionIds)->get();

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

                        // Add newly created role ID so workflow step mapping can record it
                        $assignedRoleIds[] = $crRole->id;

                        // Save permission IDs to label
                        $label->update(['permissions' => $selectedPermissionIds]);
                    }
                }

                // User & Role Permission Assignments
                if ($isSpecificUserMode && !empty($selectedUserIds)) {
                    $users = User::select('id')->whereIn('id', $selectedUserIds)->get();
                    if (!empty($permissionsSelection[$i]) && isset($crRole)) {
                        // MODE 2: Assign newly created custom role to specific selected users
                        app(PermissionRegistrar::class)->setPermissionsTeamId($schemeId);
                        foreach ($users as $user) {
                            if (!$user->hasRole($crRole)) {
                                $user->assignRole($crRole);
                            }
                        }
                    } else {
                        // MODE 1: Assign direct module access permission to specific selected users
                        foreach ($users as $user) {
                            $user->givePermissionWithScheme($permModel->id, $schemeId);
                        }
                    }
                } else {
                    // Default Mode: Assign module access permission to assigned roles globally
                    if (!empty($assignedRoleIds)) {
                        $roles = Role::select('id', 'name', 'guard_name')->whereIn('id', $assignedRoleIds)->get();
                        foreach ($roles as $role) {
                            if (!$role->hasPermissionTo($moduleAccessPerm)) {
                                $role->givePermissionTo($moduleAccessPerm);
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
            }

            DB::commit();

            $this->already = true;
            $this->isEdit = true;
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
