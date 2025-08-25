<?php

namespace App\Livewire\ProcessApplication;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\DraftBeneficiaryPersonal;

class DraftApplicationView extends Component
{
    public $applicationId;
    public $application;

    public function mount($id)
    {
        // dump($id);
        $this->applicationId = $id;

        $this->application = DraftBeneficiaryPersonal::with('relationships')->findOrFail($id);
        // dump($this->application);

    }

    public function openActionModal()
    {

        $this->dispatch('openBulkActionModal', selectedIds: [$this->application->id]);
    }

    #[On('actionPerformedAndRedirect')]
    public function navigateToTablePage()
    {

        session()->flash('success', 'The application has been successfully processed.');
        return redirect()->route('submitted-list');
    }

    public function render()
    {
        return view('livewire.process-application.draft-application-view');
    }
}
