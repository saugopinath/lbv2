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
