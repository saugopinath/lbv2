<?php

namespace App\Livewire;

use App\Models\Role;
use App\Models\WorkflowStep;
use App\Models\WorkflowsteproleMapping;
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
                ->where('rank', '<', $this->workflowStep->rank)
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

        DB::beginTransaction();
        try {
            $sameLevelRoleId = 0;
            $nextLevelRoleId = 0;
            if ($this->workflowStep->parent_id === null) {
                $sameLevelRoleId = - ($this->workflowStep->scheme_id);
                $nextLevelRoleId = 0;
            } else {
                $nextLevelRoleId = $this->workflowStep->parent_id;
                $sameLevelRoleId = $this->workflowStep->parent->parent_id ?? 0;
            }

            WorkflowsteproleMapping::where('workflow_step_id', $this->workflowStep->id)->get()->each->delete();

            foreach ($this->selectedRoles as $roleId) {
                $roleRank = Role::where('id', $roleId)->value('rank');
               
                WorkflowsteproleMapping::create([
                    'workflow_step_id'   => $this->workflowStep->id,
                    'role_id'            => $roleId,
                    'rank'               => $roleRank,
                    'scheme_id'          => $this->workflowStep->scheme_id,
                    'same_level_role_id' => $sameLevelRoleId,
                    'next_level_role_id' => $nextLevelRoleId,
                    'is_first_step'      => $this->workflowStep->is_first,
                    'is_final_step'      => $this->workflowStep->is_last,
                ]);
            }

            DB::commit();

            $this->close();
            $this->dispatch('toastr', [
                'type' => 'success',
                'message' => 'Role assigned successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('toastr', [
                'type' => 'error',
                'message' => 'Failed to assign roles: ' . $e->getMessage()
            ]);
        }
    }
    public function render()
    {
        return view('livewire.openassignworkflow-modal');
    }
}
