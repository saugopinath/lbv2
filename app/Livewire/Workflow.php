<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Scheme;
use App\Models\DynamicWorkflowModule;

class Workflow extends Component
{
    public $schemeData = false;
    public $schemeId;
    // public $schemeName = null;
    public $moduleData = false;
    public $moduleId;
    public bool $isEdit = false;

    public function mount()
    {
        $schemeIdParam = request()->query('scheme_id');
        $moduleIdParam = request()->query('module_id');

        if ($schemeIdParam && $moduleIdParam) {
            $this->isEdit = true;
            $schemeId = null;
            $moduleId = null;

            try {
                $schemeId = \Illuminate\Support\Facades\Crypt::decryptString($schemeIdParam);
            } catch (\Exception $e) {
                $schemeId = $schemeIdParam;
            }

            try {
                $moduleId = \Illuminate\Support\Facades\Crypt::decryptString($moduleIdParam);
            } catch (\Exception $e) {
                $moduleId = $moduleIdParam;
            }

            if ($schemeId && $moduleId) {
                $scheme = Scheme::select('id', 'name')->find($schemeId);
                $module = DynamicWorkflowModule::select('id', 'module_name', 'module_code')->find($moduleId);

                if ($scheme && $module) {
                    $this->schemeId = $scheme->id;
                    $this->schemeData = [
                        'scheme_id' => $scheme->id,
                        'scheme_name' => $scheme->name,
                    ];
                    $this->moduleId = $module->id;
                    $this->moduleData = [
                        'module_id' => $module->id,
                        'module_name' => $module->module_name,
                        'module_code' => $module->module_code,
                    ];
                }
            }
        }
    }
    // public $moduleName;
    #[On('module-selected')]
    public function handlemoduleData($moduleData)
    {
        if ($moduleData) {
            $this->moduleData = $moduleData;
            $this->moduleId = $moduleData['module_id'];
            // $this->moduleName = $moduleData['module_name'];
        } else {
            $this->moduleData = false;
        }
    }
    #[On('selectedScheme')]
    public function updateschemeData($schemeData)
    {
        if ($schemeData) {
            $this->schemeData = $schemeData;
            $this->schemeId = $schemeData['scheme_id'];
            // $this->schemeName = $schemeData['scheme_name'];
        } else {
            $this->schemeData = false;
        }
    }
    public function render()
    {
        return view('livewire.workflow');
    }
}
