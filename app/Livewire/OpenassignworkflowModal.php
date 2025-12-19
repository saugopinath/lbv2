<?php

namespace App\Livewire;

use App\Models\Role;
use App\Models\WorkflowStep;
use Livewire\Component;
use Livewire\Attributes\On;

class OpenassignworkflowModal extends Component
{
    public bool $isOpen = false;
    public $workflowStep, $roles, $name, $selectedRoles = [];

    #[On('openassignworkflowModal')]
    public function assignWorkflow($id)
    {
        $this->workflowStep = WorkflowStep::with('roles')->find($id);

        if ($this->workflowStep) {
            $this->name = $this->workflowStep->label;
            $this->isOpen = true;
            $this->roles = Role::whereDoesntHave('workflowSteps', function ($query) use ($id) {
                $query->where('workflow_steps.scheme_id', $this->workflowStep->scheme_id);
                $query->where('workflow_steps.id', '!=', $id);
            })->get();
            $this->selectedRoles = $this->workflowStep->roles->pluck('id')->map(fn($id) => (string) $id)->toArray();
        }
    }
    public function close()
    {
        $this->isOpen = false;
        $this->reset(['selectedRoles', 'workflowStep']);
    }

    public function save()
    {
        $this->validate([
            'selectedRoles' => 'required|array|size:1',
        ]);
        $rank = $this->workflowStep->rank;
        $sameLabelRoleId = 0;
        $nextLabelRoleId = 0;
        if ($this->workflowStep->parent_id === null) {
            $sameLabelRoleId = - ($this->workflowStep->scheme_id);
            $nextLabelRoleId = 0;
        } else {
            $nextLabelRoleId = $this->workflowStep->parent_id;
            $sameLabelRoleId = $this->workflowStep->parent->parent_id ?? 0;
        }
        $syncData = [];
        foreach ($this->selectedRoles as $roleId) {
            $syncData[$roleId] = [
                'rank' => $rank,
                'same_label_role_id' => $sameLabelRoleId,
                'next_label_role_id' => $nextLabelRoleId,
            ];
        }
        $this->workflowStep->roles()->sync($syncData);
        $this->close();
    }

    public function render()
    {
        return view('livewire.openassignworkflow-modal');
    }
}
