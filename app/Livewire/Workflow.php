<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class Workflow extends Component
{
    public $schemeData = false;
    public $schemeId;
    // public $schemeName = null;
    public $moduleData = false;
    public $moduleId;

    public function mount()
    {
        $schemeIdParam = request()->query('scheme_id');
        $moduleIdParam = request()->query('module_id');

        if ($schemeIdParam && $moduleIdParam) {
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
                $scheme = \App\Models\Scheme::find($schemeId);
                $module = \App\Models\DynamicWorkflowModule::find($moduleId);

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
