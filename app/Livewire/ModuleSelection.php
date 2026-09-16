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
    // public $moduleName;
    public int $schemeId;

    public function mount($schemeData)
    {
        $this->schemeId = $schemeData['scheme_id'];
        $this->moduleList = DynamicWorkflowModule::select('id', 'module_name', 'module_code')->get();
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
    }

    public function saveModule()
    {
        if ($this->isNewModule) {
            $this->validate([
                'newModuleName' => 'required|min:3|max:255|unique:dynamic_workflow_modules,module_name',
                'newModuleCode' => 'required|min:3|max:50|unique:dynamic_workflow_modules,module_code',
            ]);

            $module = DynamicWorkflowModule::create([
                'module_name' => $this->newModuleName,
                'module_code' => $this->newModuleCode,
                'is_active' => true,
                'created_by' => Auth::id()
            ]);
            $this->moduleId = $module->id;
            $this->moduleData = ['module_id' => $this->moduleId, 'module_name' => $this->newModuleName, 'module_code' => $this->newModuleCode];
        } else {
            $this->validate(['moduleId' => 'required|exists:dynamic_workflow_modules,id']);
            $module = collect($this->moduleList)->firstWhere('id', (int) $this->moduleId);
            $moduleName = data_get($module, 'module_name');
            $moduleCode = data_get($module, 'module_code');
            $this->moduleData = ['module_id' => $this->moduleId, 'module_name' => $moduleName, 'module_code' => $moduleCode];
        }
        $this->dispatch('module-selected', $this->moduleData);
    }
}
