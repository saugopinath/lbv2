<?php

namespace App\Livewire\ProcessApplication;

use Livewire\Component;
use App\Models\DraftBeneficiaryPersonal;

class DraftApplicationView extends Component
{
    public $applicationId;
    public $application;

    public function mount($id)
    {
        $this->applicationId = $id;
        $this->application = DraftBeneficiaryPersonal::with('ben_relationships')->findOrFail($id);

    }

    public function render()
    {
        return view('livewire.process-application.draft-application-view');
    }
}
