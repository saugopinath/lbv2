<?php

namespace App\Livewire;

use App\Services\WorkflowService;
use Livewire\Attributes\On;
use Livewire\Component;

class ApplicationLists extends Component
{
    public bool $schemeData = false;

    public $schemeId;

    public $schemeName = null;

    #[On('selectedScheme')]
    public function updateschemeData($schemeData, WorkflowService $workflowService)
    {
        if ($schemeData) {
            $data = $workflowService->getLabelRoles($schemeData['scheme_id']);
            if ($data) {
                $this->schemeData = true;
                $this->schemeId = $schemeData['scheme_id'];
                $this->schemeName = $schemeData['scheme_name'];
            } else {
                $this->dispatch('toastr', [
                    'type' => 'warning',
                    'message' => 'Work Flow Step Still Not Configured!',
                ]);
            }
        } else {
            $this->schemeData = false;
        }
    }

    public function render()
    {
        return view('livewire.application-lists');
    }
}
