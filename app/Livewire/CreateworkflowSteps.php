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

        $steps = WorkflowStep::where('scheme_id', $this->schemeId)
            ->orderBy('rank')
            ->pluck('label')->toArray();

        // $roles = Role::select('name', 'id')->whereNotNull('rank')->orderBy('rank')->get();
        $roles = Role::orderBy('name')->pluck('name', 'id')->toArray();
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

        dd(array_filter($this->roleSelection), array_filter($this->permissionsSelection, fn($item) => !empty($item)), $this);

        DB::beginTransaction();
        try {
            $parentId = null;
            $totalSteps = count($this->labels);
            $data = [];
            for ($i = 0; $i < $totalSteps; $i++) {
                $data[] = [
                    'scheme_id' => $this->schemeId,
                    'rank' => $i,
                    'label' => $this->labels[$i],
                    'parent_id' => $parentId,
                    'is_first' => ($i === 0),
                    'is_last' => ($i === $totalSteps - 1),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                // $step = new WorkflowStep();
                // $step->scheme_id = $this->schemeId;
                // $step->rank = $i + 1;
                // $step->label = $this->labels[$i];
                // $step->parent_id = $parentId;
                // $step->is_first = ($i === 0);
                // $step->is_last = ($i === $totalSteps - 1);
                // $step->save();
                // $parentId = $step->id;
            }
            WorkflowStep::insert($data);
            DB::commit();
            $this->already = true;
            $this->dispatch('toastr', [
                'type' => 'success',
                'message' => 'Workflow steps created successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->already = false;
            $this->dispatch('toastr', [
                'type' => 'error',
                'message' => 'Something went wrong. Please try again.'
            ]);
        }

        try {
            $schemeId = $this->schemeId;
            $moduleId = $this->moduleId;
            $moduleCode = $this->moduleCode;
            $stepCount = $this->noofSteps;
            $roleSelection = array_filter($this->roleSelection);
            $permissionsSelection = array_filter($this->permissionsSelection, fn($item) => !empty($item));
            $schemeModule = DynamicWorkflowSchemeModule::updateOrCreate(
                [
                    'scheme_id' => $schemeId,
                    'module_id' => $moduleId,
                ],
                [
                    'scheme_id' => $schemeId,
                    'module_id' => $moduleId,
                    'main_module_code' => $moduleCode,
                    'step_count' => $stepCount,
                ]
            );
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
                $successRank = ($i < count($stepCount) - 1) ? ($i + 2) * 10 : 0;
                $revertRank = ($i > 0) ? $i * 10 : - ($this->schemeId);
                $opTypeId = null;

                if ($parent) {
                    $labelSlug = strtolower(str_replace(' ', '_', $this->labels[$i]));
                    $codemaster = Codemaster::select('id')->where('parent_id', $parent_id)
                        ->where('short_name', $labelSlug)
                        ->first();
                    if (!$codemaster) {
                        if (!$maxCode) {
                            $maxCode = ($parent_code * 10);
                        }
                        $codemaster = Codemaster::create([
                            'name' => strtoupper($this->labels[$i]),
                            'short_name' => strtolower($moduleCode) . '_' . $labelSlug,
                            'parent_id' => $parent_id,
                            'parent_short_code' => 'dynamic_op_type',
                            'code' => $maxCode + 1,
                            'is_active' => 1,
                        ]);
                    }
                    $opTypeId = $codemaster->id;
                }

                $label = DynamicWorkflowLabel::create([
                    'scheme_id' => $schemeId,
                    'module_id' => $moduleId,
                    'label_name' => $this->labels[$i],
                    'op_type_id' => $opTypeId,
                ]);
                $data = [];
                if (!empty($roleSelection) && isset($roleSelection[$i])) {
                    foreach ($roleSelection[$i] as $roleId) {
                        $data[] = [
                            'scheme_id' => $schemeId,
                            'module_id' => $schemeModule->id,
                            'workflow_step_id' => $label->id,
                            'role_id' => $roleId,
                            'rank' => $rank,
                            'next_level_role_id' => $successRank,
                            'same_level_role_id' => $revertRank,
                            'is_final_step' => ($i == count($stepCount) - 1),
                            'action_type' => null,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }
                    workflowstepRolemapping::insert($data);
                }
                $dt = [];
                if (!empty($permissionsSelection) && isset($permissionsSelection[$i])) {
                    $roleCreated = [];
                    foreach ($permissionsSelection[$i] as $permId => $val) {
                        if (is_numeric($permId)) {
                            $permission = Permission::select('id', 'name')->find($permId);
                            if ($permission) {
                                $roleChkFlag = $this->labels[$i] . '_' . $i;
                                if (!array_key_exists($roleChkFlag, $roleCreated)) {
                                    $crRole = Role::create([
                                        'name' => $this->labels[$i],
                                        'guard_name' => 'web',
                                        'rank' => Role::max('rank') + 1,
                                    ]);
                                    $crRoleId = $crRole->id;
                                    $x = [$roleChkFlag => $crRoleId];
                                    $roleCreated[$roleChkFlag] = $x;
                                } else {
                                    $crRoleId = $roleCreated[$roleChkFlag];
                                }
                            }
                        }
                    }

                    if (!empty($this->permissionsSelection[$i])) {
                        // 1. Extract only the checked permission IDs
                        $selectedPermissionIds = array_keys(array_filter($this->permissionsSelection[$i]));

                        if (!empty($selectedPermissionIds)) {
                            // 2. Create or find the Role once for this step
                            $crRole = Role::firstOrCreate(
                                [
                                    'name'       => $this->labels[$i],
                                    'guard_name' => 'web',
                                ],
                                [
                                    'rank'       => (Role::max('rank') ?? 0) + 1,
                                ]
                            );

                            // 3. Attach all permissions to the role in 1 batch query
                            $crRole->syncPermissions($selectedPermissionIds);

                            // 4. Clean integer ID ready for your workflow step record
                            $crRoleId = $crRole->id;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
        }
    }

    public function render()
    {
        return view('livewire.createworkflow-steps');
    }
}
