<?php

namespace App\Livewire;

use App\Models\Role;
use App\Models\WorkflowStep;
use App\Models\workflowstepRolemapping;
use Livewire\Component;
use Livewire\Attributes\On;

class OpenassignworkflowModal extends Component
{
    public bool $isOpen = false;
    public $workflowStep, $roles, $name, $selectedRoles = [];

    #[On('openassignworkflowModal')]
    public function assignWorkflow($id)
    {
        $this->workflowStep = WorkflowStep::find($id);
        $this->name = $this->workflowStep->label;
        $this->isOpen = true;
        $this->roles = Role::all();
    }
    public function close()
    {
        $this->isOpen = false;
    }

    public function save()
    {
        $this->validate([
            'selectedRoles' => 'required|array|min:1',
        ]);
        $ids = $this->selectedRoles;
        foreach ($ids as $id) {
            $step = new workflowstepRolemapping();
            $step->rank = $this->workflowStep->rank;
            $step->workflow_step_id = $this->workflowStep->id;
            $step->role_id = (int)$id;
            $step->same_label_role_id = -$this->workflowStep->scheme_id;
            $step->next_label_role_id = 2;
            dd($this->workflowStep, $step);
            // $step->save();
        }
    }

    public function render()
    {
        return view('livewire.openassignworkflow-modal');
    }
}
