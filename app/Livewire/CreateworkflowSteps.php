<?php

namespace App\Livewire;

use App\Models\{Role, Permission};
use App\Models\WorkflowStep;
use App\Models\{User, Codemaster, DynamicWorkflowLabel, DynamicWorkflowModule, DynamicWorkflowSchemeModule, UserRoleSchemeOfficeMapping, workflowstepRolemapping};
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use App\Attributes\Loggable;
use Illuminate\Support\Facades\Auth;

class CreateworkflowSteps extends Component
{
    public $schemeId;
    public $noofSteps;
    public $labels = [];
    public bool $already = false;
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

    public function rendering()
    {
        for ($i = 0; $i < $this->noofSteps; $i++) {
            if (!isset($this->roleSelection[$i]) || !is_array($this->roleSelection[$i])) {
                $this->roleSelection[$i] = [];
            } elseif (!isset($this->roleSelection[$i])) {
                $this->roleSelection[$i] = [];
            }

            if (!isset($this->permissionsSelection[$i])) {
                $this->permissionsSelection[$i] = [];
            }
        }
    }

    public function mount($schemeData, $moduleData)
    {
        $this->schemeId = $schemeData['scheme_id'];
        $this->moduleId = $moduleData['module_id'];
        $this->moduleCode = $moduleData['module_code'];

        // FIX: Load existing steps from DynamicWorkflowLabel instead of WorkflowStep to match save()
        $steps = DynamicWorkflowLabel::where('scheme_id', $this->schemeId)
            ->where('module_id', $this->moduleId)
            ->orderBy('id')
            ->pluck('label_name')->toArray();

        // FIX: Enforce Original Role Rank Hierarchy for the UI alert by querying whereNotNull('rank')
        $roles = Role::whereNotNull('rank')->orderBy('rank')->pluck('name', 'id')->toArray();
        $permissions = Permission::select('name', 'id')->orderBy('name')->get();

        if (!empty($roles)) {
            $this->originalrolerank = true;
            $this->roles = $roles;
        }
        if (!empty($permissions)) {
            $this->permissionsList = $permissions;
        }
        if (!empty($steps)) {
            $this->noofSteps = count($steps);
            $this->labels = $steps;
            $this->already = true;
        }

        foreach ($this->labels as $index => $value) {
            $this->assignRule[$index] = "1";
            $this->existingRole[$index] = true;
            $this->newRole[$index] = false;
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
        $existing = $this->labels;
        $this->labels = [];
        $this->roleSelection = [];
        $this->permissionsSelection = [];
        for ($i = 0; $i < $value; $i++) {
            $this->labels[$i] = $existing[$i] ?? '';
            $this->assignRule[$i] = "1";
            $this->existingRole[$i] = true;
            $this->newRole[$i] = false;
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

                $label = DynamicWorkflowLabel::create([
                    'scheme_id'  => $schemeId,
                    'module_id'  => $moduleId,
                    'label_name' => $this->labels[$i],
                    'op_type_id' => $opTypeId,
                ]);

                $assignedRoleIds = [];

                // FEATURE: Auto-create the module access permission
                $moduleAccessPerm = strtolower(str_replace(' ', '_', $moduleCode)) . '_access';
                Permission::firstOrCreate(['name' => $moduleAccessPerm, 'guard_name' => 'web']);

                // MODE 1: Existing Role Selection
                if (!empty($roleSelection[$i])) {
                    $assignedRoleIds = (array) $roleSelection[$i];
                }

                // MODE 2: Custom Role Creation via Permissions
                if (!empty($permissionsSelection[$i])) {
                    $selectedPermissionIds = array_keys(array_filter($permissionsSelection[$i]));

                    if (!empty($selectedPermissionIds)) {
                        // FIX: Prepend scheme and module code to make role names unique across workflows
                        $uniqueRoleName = strtoupper($moduleCode) . '_' . $schemeId . '_' . $this->labels[$i];
                        $crRole = Role::firstOrCreate(
                            [
                                'name'       => $uniqueRoleName,
                                'guard_name' => 'web',
                            ],
                            [
                                'rank'       => (Role::max('rank') ?? 0) + 1,
                            ]
                        );

                        $permissions = Permission::whereIn('id', $selectedPermissionIds)->get();

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

                // Save Workflow Step Role Mappings for whichever mode was used
                if (!empty($assignedRoleIds)) {
                    $mappingData = [];
                    foreach ($assignedRoleIds as $roleId) {
                        // FEATURE: Give the assigned role the module access permission
                        $role = Role::find($roleId);
                        if ($role && !$role->hasPermissionTo($moduleAccessPerm)) {
                            $role->givePermissionTo($moduleAccessPerm);
                        }

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
            $this->dispatch('toastr', [
                'type'    => 'success',
                'message' => 'Workflow steps created successfully!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            $this->already = false;
            $this->dispatch('toastr', [
                'type'    => 'error',
                'message' => 'Something went wrong. Please try again.',
            ]);
        }
    }

    public function render()
    {
        return view('livewire.createworkflow-steps');
    }
}
