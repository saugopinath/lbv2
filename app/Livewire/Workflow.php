<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
class Workflow extends Component
{
    public bool $schemeData = false;
    public $schemeId, $schemeName = null;
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
        return view('livewire.workflow');
    }
}
