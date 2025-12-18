<?php

namespace App\Livewire;

use App\Models\Role;
use App\Models\WorkflowStep;
use Livewire\Component;
use Livewire\Attributes\On;

class OpenassignworkflowModal extends Component
{
    public bool $isOpen = false;
    public $roles,$name;

    #[On('openassignworkflowModal')]
    public function assignWorkflow($id)
    {
        $this->name = WorkflowStep::find($id)->label;
        $this->isOpen = true;
        $this->roles = Role::all();
       
    }
     public function close()
    {
        $this->isOpen = false;
    }

    public function render()
    {
        return view('livewire.openassignworkflow-modal');
    }
}
