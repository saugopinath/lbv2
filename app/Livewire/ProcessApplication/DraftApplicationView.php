<?php

namespace App\Livewire\ProcessApplication;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\DraftBeneficiaryPersonal;
use Illuminate\Support\Facades\Crypt;
class DraftApplicationView extends Component
{
    public $applicationId;
    public $application;

    public function mount($id)
    {
        // dump($id);
        $this->applicationId = Crypt::decryptString($id);

        $this->application = DraftBeneficiaryPersonal::with('relationships')->findOrFail($this->applicationId);
        // dd($this->application, $this->application->toSql());
        // dd($this->application, json_encode($this->application));
        // $jsondata = json_encode($this->application);
        // dump($jsondata);
        // dd(json_decode($jsondata));
    }

    public function openActionModal()
    {
        $this->dispatch('hideLoader');
        $this->dispatch('openBulkActionModal', selectedIds: [$this->application->application_id]);
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
