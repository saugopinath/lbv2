<?php

namespace App\Livewire\DynamicWorkflow;

use App\Models\Scheme;
use App\Models\Role;
use App\Models\DynamicWorkflowModule;
use App\Models\DynamicWorkflowLabel;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class WorkflowWizard extends Component
{
    public $currentTab = 1;
    public $totalTabs = 3;

    // Tab 1: Module Management
    public $selectedScheme;
    public $selectedModule;
    public $isNewModule = false;
    public $newModuleName;
    public $newModuleCode;
    public $moduleList = [];

    // Tab 2: Step Count & Naming
    public $stepCount = 1;
    public $stepNames = [];

    // Tab 3: Detailed Config
    public $finalSteps = [];
    public $roles = [];

    public function mount()
    {
        $this->roles = Role::orderBy('name')->get();
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
                // লেবেলগুলো লোড করা
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
        if ($this->stepCount < 1) $this->stepCount = 1;
        if ($this->stepCount > 10) $this->stepCount = 10;

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
                'newModuleCode' => 'required'
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
            'stepNames.*' => 'required'
        ]);

        $this->currentTab = 3;
        
        $existingMappings = [];
        if (!$this->isNewModule) {
            $existingMappings = \App\Models\workflowstepRolemapping::where('module_id', $this->selectedModule)
                ->orderBy('rank', 'asc')
                ->get()
                ->keyBy('rank');
        }

        $this->finalSteps = [];
        foreach ($this->stepNames as $index => $label) {
            $rank = ($index + 1) * 10;
            $mapping = $existingMappings[$rank] ?? null;

            $this->finalSteps[$index] = [
                'rank' => (int) $rank,
                'label' => $label,
                'role_id' => $mapping ? $mapping->role_id : '',
                'is_final' => ($index == $this->stepCount - 1),
                'success_rank' => ($index < $this->stepCount - 1) ? ($index + 2) * 10 : null,
                'revert_rank' => ($index > 0) ? $index * 10 : null
            ];
        }
    }

    public function saveWorkflow()
    {
        $this->validate([
            'selectedScheme' => 'required',
            'finalSteps.*.role_id' => 'required'
        ]);

        // DEBUG: ইস্যু দেখার জন্য ডাটা চেক করা
        // dd($this->finalSteps, $this->selectedModule, $this->isNewModule);

        if (!Auth::check()) {
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
                    'created_by' => Auth::id()
                ]);
            } else {
                $module = DynamicWorkflowModule::find($this->selectedModule);
                $module->update(['step_count' => $this->stepCount]);
            }
            $module->steps()->delete();
            DynamicWorkflowLabel::where('module_id', $module->id)->delete();
            foreach ($this->finalSteps as $index => $stepData) {
                $rank = ($index + 1) * 10;
                $successRank = ($index < count($this->finalSteps) - 1) ? ($index + 2) * 10 : null;
                $revertRank = ($index > 0) ? $index * 10 : null;

                $label = DynamicWorkflowLabel::create([
                    'scheme_id' => $this->selectedScheme,
                    'module_id' => $module->id,
                    'label_name' => $stepData['label']
                ]);

                \App\Models\workflowstepRolemapping::updateOrCreate(
                    [
                        'scheme_id' => $this->selectedScheme,
                        'module_id' => $module->id,
                        'rank' => $rank
                    ],
                    [
                        'workflow_step_id' => $label->id,
                        'role_id' => $stepData['role_id'],
                        'next_label_role_id' => $successRank,
                        'same_label_role_id' => $revertRank,
                        'is_final_step' => ($index == count($this->finalSteps) - 1),
                        'action_type' => null
                    ]
                );
            }
            DB::commit();
            // $this->dispatch('refresh-page');
            $this->dispatch('toast', 'success', 'Workflow Master & Steps Configured Perfectly!');
            $this->currentTab = 1;
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
            'schemes' => Scheme::where('is_active', true)->get()
        ]);
    }
}
