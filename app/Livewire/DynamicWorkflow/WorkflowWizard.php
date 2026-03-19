<?php

namespace App\Livewire\DynamicWorkflow;

use App\Models\Scheme;
use App\Models\Role;
use App\Models\DynamicWorkflowModule;
use App\Models\DynamicWorkflowStep;
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
        $this->finalSteps = [];
        foreach ($this->stepNames as $index => $label) {
            $rank = ($index + 1) * 10;
            $this->finalSteps[$index] = [
                'rank' => $rank,
                'label' => $label,
                'role_id' => '',
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

        if (!Auth::check()) {
            session()->flash('error', 'Authentication session expired. Please login again.');
            return;
        }

        DB::beginTransaction();
        try {
            // ১. মডিউল মাস্টার আপডেট
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

            // ২. ডিলিট এবং ক্লিনআপ
            $module->steps()->delete();
            DynamicWorkflowLabel::where('module_id', $module->id)->delete();

            // ৩. লুপ চালিয়ে লেবেল এবং স্টেপ সেভ করা
            foreach ($this->finalSteps as $stepData) {
                // লেবেল টেবিল এ ইনসার্ট
                $label = DynamicWorkflowLabel::create([
                    'scheme_id' => $this->selectedScheme,
                    'module_id' => $module->id,
                    'label_name' => $stepData['label']
                ]);

                // স্টেপ টেবিল এ ইনসার্ট (লেবেল আইডি সহ)
                DynamicWorkflowStep::create([
                    'scheme_id' => $this->selectedScheme,
                    'module_id' => $module->id,
                    'label_id' => $label->id,
                    'rank' => $stepData['rank'],
                    'role_id' => $stepData['role_id'],
                    'success_rank' => $stepData['success_rank'],
                    'revert_rank' => $stepData['revert_rank'],
                    'is_final_step' => $stepData['is_final']
                ]);
            }

            DB::commit();
            session()->flash('success', 'Workflow Master & Steps Configured Perfectly!');
            $this->currentTab = 1;
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', $e->getMessage());
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
