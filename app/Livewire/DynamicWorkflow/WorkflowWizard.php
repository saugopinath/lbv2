<?php

namespace App\Livewire\DynamicWorkflow;

use App\Models\DynamicWorkflowLabel;
use App\Models\DynamicWorkflowModule;
use App\Models\Permission;
use App\Models\workflowstepRolemapping;
use App\Models\Role;
use App\Models\Scheme;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class WorkflowWizard extends Component
{
    public $currentTab = 1;
    public $totalTabs = 3;

    public $selectedScheme;
    public $selectedModule;
    public $isNewModule = false;
    public $newModuleName;
    public $newModuleCode;
    public $moduleList = [];

    public $stepCount = 1;
    public $stepNames = [];

    public $finalSteps = [];
    public $roles = [];

    public function mount()
    {
        $this->roles = Role::orderBy('name')->pluck('name', 'id')->toArray();
    }

    public function updatedSelectedScheme()
    {
        $this->moduleList = DynamicWorkflowModule::where('scheme_id', $this->selectedScheme)->get();
        $this->selectedModule = null;
        $this->isNewModule = false;
    }

    public function updatedSelectedModule($value)
    {
        if ($value && $value != 'new') {
            $module = DynamicWorkflowModule::with('steps.module')->find($value);

            if ($module) {
                $this->stepCount = $module->step_count;

                $labels = DynamicWorkflowLabel::where('module_id', $module->id)
                    ->orderBy('id', 'asc')
                    ->pluck('label_name')
                    ->toArray();

                $this->stepNames = $labels;
            }
        }
    }

    public function incrementStepCount()
    {
        if ($this->stepCount < 10) {
            $this->stepCount++;
            $this->updateStepNames();
        }
    }

    public function decrementStepCount()
    {
        if ($this->stepCount > 1) {
            $this->stepCount--;
            $this->updateStepNames();
        }
    }

    public function updatedStepCount($value)
    {
        $this->stepCount = (int) $value;
        $this->updateStepNames();
    }

    protected function updateStepNames()
    {
        if ($this->stepCount < 1) {
            $this->stepCount = 1;
        }

        if ($this->stepCount > 10) {
            $this->stepCount = 10;
        }

        $newStepNames = [];

        for ($i = 0; $i < $this->stepCount; $i++) {
            $newStepNames[$i] = $this->stepNames[$i] ?? '';
        }

        $this->stepNames = $newStepNames;
    }

    public function moveToNaming()
    {
        if ($this->isNewModule) {
            $this->validate([
                'newModuleName' => 'required|min:3',
                'newModuleCode' => 'required',
            ]);
        } else {
            $this->validate(['selectedModule' => 'required']);
        }

        $this->currentTab = 2;

        if (empty($this->stepNames)) {
            $this->stepNames = array_fill(0, $this->stepCount, '');
        }
    }

    public function moveToConfig()
    {
        $this->validate([
            'stepCount' => 'required|integer|min:1',
            'stepNames.*' => 'required',
        ]);

        $this->currentTab = 3;

        $existingMappings = collect();

        if (! $this->isNewModule) {
            $existingMappings = workflowstepRolemapping::where('module_id', $this->selectedModule)
                ->orderBy('rank', 'asc')
                ->get()
                ->groupBy('rank');
        }

        $this->finalSteps = [];

        foreach ($this->stepNames as $index => $label) {
            $rank = ($index + 1) * 10;
            $mappings = $existingMappings->get($rank, collect());
            $firstMapping = $mappings->first();

            $this->finalSteps[$index] = [
                'rank' => (int) $rank,
                'label' => $label,
                'role_ids' => $mappings
                    ->pluck('role_id')
                    ->map(fn($roleId) => (string) $roleId)
                    ->values()
                    ->all(),
                'is_final' => ($index == $this->stepCount - 1),
                'success_rank' => $firstMapping?->next_label_role_id ?? (($index < $this->stepCount - 1) ? ($index + 2) * 10 : null),
                'revert_rank' => $firstMapping?->same_label_role_id ?? (($index > 0) ? $index * 10 : null),
            ];
        }
    }

    public function saveWorkflow()
    {
        $this->validate([
            'selectedScheme' => 'required',
            'finalSteps.*.role_ids' => 'required|array|min:1',
            'finalSteps.*.role_ids.*' => 'exists:roles,id',
        ]);

        if (! Auth::check()) {
            session()->flash('error', 'Authentication session expired. Please login again.');

            return;
        }

        DB::beginTransaction();

        try {
            if ($this->isNewModule) {
                $module = DynamicWorkflowModule::create([
                    'scheme_id' => $this->selectedScheme,
                    'module_code' => strtoupper($this->newModuleCode),
                    'module_name' => $this->newModuleName,
                    'step_count' => $this->stepCount,
                    'created_by' => Auth::id(),
                ]);
            } else {
                $module = DynamicWorkflowModule::find($this->selectedModule);
                $module->update(['step_count' => $this->stepCount]);
            }

            $module->steps()->delete();
            DynamicWorkflowLabel::where('module_id', $module->id)->delete();

            foreach ($this->finalSteps as $index => $stepData) {
                $rank = ($index + 1) * 10;
                $successRank = ($index < count($this->finalSteps) - 1) ? ($index + 2) * 10 : 0;
                $revertRank = ($index > 0) ? $index * 10 : null;

                $label = DynamicWorkflowLabel::create([
                    'scheme_id' => $this->selectedScheme,
                    'module_id' => $module->id,
                    'label_name' => $stepData['label'],
                ]);

                foreach ($stepData['role_ids'] as $roleId) {
                    workflowstepRolemapping::create([
                        'scheme_id' => $this->selectedScheme,
                        'module_id' => $module->id,
                        'workflow_step_id' => $label->id,
                        'rank' => $rank,
                        'role_id' => $roleId,
                        'next_label_role_id' => $successRank,
                        'same_label_role_id' => $revertRank,
                        'is_final_step' => ($index == count($this->finalSteps) - 1),
                        'action_type' => null,
                    ]);

                    $labelSlug = strtolower(str_replace(' ', '_', $stepData['label']));
                    $permissionName = "{$module->module_code}.{$labelSlug}";
                    $permission = Permission::firstOrCreate([
                        'name' => $permissionName,
                        'guard_name' => 'web'
                    ]);
                    $role = Role::find($roleId);
                    if ($role && !$role->hasPermissionTo($permissionName)) {
                        $role->givePermissionTo($permission);
                    }
                }
            }

            DB::commit();
            $this->dispatch('toast', 'success', 'Workflow Master & Steps Configured Perfectly!');
            $this->reset(['selectedScheme', 'selectedModule', 'isNewModule', 'newModuleName', 'newModuleCode', 'stepCount', 'stepNames', 'finalSteps']);
            $this->currentTab = 1;
            $this->moduleList = [];
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('toast', 'error', $e->getMessage());
        }
    }

    public function goBack()
    {
        if ($this->currentTab > 1) {
            $this->currentTab--;
        }
    }
    public function render()
    {
        return view('livewire.dynamic-workflow.workflow-wizard', [
            'schemes' => Scheme::where('is_active', true)->get(),
        ]);
    }
}
