<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Codemaster;
class CmoGrievanceWorkflowDropdown extends Component
{
    public $process_type, $types;
    public function submit()
    {
        $this->dispatch('processTypeChanged', $this->process_type);
    }
    public function render()
    {
        $this->types = Codemaster::where('code', 330)->first()->children()->get();
        return view('livewire.cmo-grievance-workflow-dropdown');
    }
}
