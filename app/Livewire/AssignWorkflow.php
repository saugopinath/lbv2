<?php

namespace App\Livewire;

use App\Models\WorkflowStep;
use Livewire\Component;

class AssignWorkflow extends Component
{
    public $schemeId;
    public bool $already = false;
    public function mount($schemeId)
    {
        $this->schemeId = $schemeId;
        $steps = WorkflowStep::where('scheme_id', $schemeId)
            ->orderBy('rank')
            ->get();
        if ($steps->isNotEmpty()) {
            $this->already = true;
        }
    }
    public function render()
    {
        return view('livewire.assign-workflow');
    }
}
