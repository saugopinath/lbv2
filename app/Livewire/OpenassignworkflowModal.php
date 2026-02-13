<?php
namespace App\Livewire;
use App\Models\Role;
use App\Models\WorkflowStep;
use Illuminate\Support\Facades\DB;
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
            $previousStepIds = WorkflowStep::where('scheme_id', $this->workflowStep->scheme_id)
                ->where('rank', '<', $this->workflowStep->rank) // IMPORTANT
                ->pluck('id');
            $previousMaxRank = DB::table('workflowstep_rolemappings')
                ->whereIn('workflow_step_id', $previousStepIds)
                ->max('rank');
            $this->roles = Role::when($previousMaxRank, function ($query) use ($previousMaxRank) {
                $query->where('rank', '>', $previousMaxRank);
            })
                ->orderBy('rank')
                ->get();
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
            'selectedRoles' => 'required|array',
        ]);
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
            $roleRank = Role::where('id', $roleId)->value('rank');
            $syncData[$roleId] = [
                'rank' => $roleRank,
                'scheme_id' => $this->workflowStep->scheme_id,
                'same_label_role_id' => $sameLabelRoleId,
                'next_label_role_id' => $nextLabelRoleId,
            ];
        }
        $this->workflowStep->roles()->sync($syncData);
        $this->close();
        $this->dispatch('toastr', [
            'type' => 'success',
            'message' => 'Role assigned successfully.'
        ]);
    }
    public function render()
    {
        return view('livewire.openassignworkflow-modal');
    }
}