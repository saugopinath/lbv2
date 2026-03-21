<?php

namespace App\Livewire;

use App\Helpers\CheckAuthHelper;
use Livewire\Component;
use Livewire\Attributes\On;

class CmoWorkflow extends Component
{
    public bool $schemeData = false;
    public $schemeId, $schemeName = null;
    public $workflow_dropdown_show;
    public function mount() {
        if (CheckAuthHelper::isCommonOperator()) {
            $this->workflow_dropdown_show = 0;
        } else {
            $this->workflow_dropdown_show = 1;
        }
    }
    #[On('selectedScheme')]
    public function updateschemeData($schemeData)
    {
        if ($schemeData) {
            $this->schemeData = true;
            $this->schemeId = $schemeData['scheme_id'];
            $this->schemeName = $schemeData['scheme_name'];
        } else {
            $this->schemeData = false;
        }
    }
    public function render()
    {
        return view('livewire.cmo-workflow');
    }
}
