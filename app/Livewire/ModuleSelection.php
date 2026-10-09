<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\DynamicWorkflowModule;
use Illuminate\Support\Facades\Auth;

class ModuleSelection extends Component
{
    public bool $isNewModule = false;
    public $moduleData = false;
    public $moduleList = [];
    public string $newModuleName = '';
    public string $newModuleCode = '';
    public int $moduleId;
    public int $schemeId;
    public string $workflowType = 'normal';

    public function mount($schemeData)
    {
        $this->schemeId = $schemeData['scheme_id'];
        $this->moduleList = DynamicWorkflowModule::select('id', 'module_name', 'module_code', 'workflow_type')->get();
    }

    public function render()
    {
        return view('livewire.module-selection');
    }

    public function updatedisNewModule($value)
    {
        if ($value == "1") {
            $this->isNewModule = true;
        } else {
            $this->isNewModule = false;
        }
        $this->reset(['moduleId', 'newModuleName', 'newModuleCode']);
        $this->workflowType = 'normal';
    }

    public function saveModule()
    {
        if ($this->isNewModule) {
            $this->validate([
                'newModuleName' => 'required|min:3|max:255|unique:dynamic_workflow_modules,module_name',
                'newModuleCode' => 'required|min:3|max:50|unique:dynamic_workflow_modules,module_code',
                'workflowType'  => 'required|in:urban,rural,normal',
            ]);

            $targetWorkflowType = strtolower($this->workflowType);

            // Validation: Only one active module of each workflow_type is allowed per scheme
            $alreadyActive = \App\Models\DynamicWorkflowSchemeModule::where('scheme_id', $this->schemeId)
                ->where('is_disabled', 0)
                ->whereHas('module', function ($q) use ($targetWorkflowType) {
                    $q->where('workflow_type', $targetWorkflowType);
                })
                ->exists();

            if ($alreadyActive) {
                $this->dispatch('toastr', [
                    'type' => 'error',
                    'message' => "An active '{$targetWorkflowType}' workflow module is already configured for this scheme. Only one active module of each type is allowed.",
                ]);
                return;
            }

            $module = DynamicWorkflowModule::create([
                'module_name'   => $this->newModuleName,
                'module_code'   => $this->newModuleCode,
                'workflow_type' => $targetWorkflowType,
                'is_active'     => true,
                'created_by'    => Auth::id()
            ]);
            $this->moduleId = $module->id;
            $this->moduleData = [
                'module_id'     => $this->moduleId,
                'module_name'   => $this->newModuleName,
                'module_code'   => $this->newModuleCode,
                'workflow_type' => $targetWorkflowType,
            ];
        } else {
            $this->validate(['moduleId' => 'required|exists:dynamic_workflow_modules,id']);
            $module = collect($this->moduleList)->firstWhere('id', (int) $this->moduleId);
            $targetWorkflowType = strtolower(data_get($module, 'workflow_type') ?: 'normal');

            // Validation: Only one active module of each workflow_type is allowed per scheme
            $alreadyActive = \App\Models\DynamicWorkflowSchemeModule::where('scheme_id', $this->schemeId)
                ->where('is_disabled', 0)
                ->where('module_id', '!=', (int) $this->moduleId)
                ->whereHas('module', function ($q) use ($targetWorkflowType) {
                    $q->where('workflow_type', $targetWorkflowType);
                })
                ->exists();

            if ($alreadyActive) {
                $this->dispatch('toastr', [
                    'type' => 'error',
                    'message' => "An active '{$targetWorkflowType}' workflow module is already configured for this scheme. Only one active module of each type is allowed.",
                ]);
                return;
            }

            $moduleName = data_get($module, 'module_name');
            $moduleCode = data_get($module, 'module_code');
            $this->moduleData = [
                'module_id'     => $this->moduleId,
                'module_name'   => $moduleName,
                'module_code'   => $moduleCode,
                'workflow_type' => $targetWorkflowType,
            ];
        }
        $this->dispatch('module-selected', $this->moduleData);
    }
}
