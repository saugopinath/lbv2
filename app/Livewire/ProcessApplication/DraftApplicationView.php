<?php

namespace App\Livewire\ProcessApplication;

use App\Models\Scheme;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\BeneficiaryPersonalDetail;
use Illuminate\Support\Facades\Crypt;

class DraftApplicationView extends Component
{
    public $applicationId;
    public $application;
    public $schemeId;
    public $schemeName;

    public function mount()
    {
        try {
            $encrypted = request()->query('application_id');
            $this->applicationId = (int) Crypt::decryptString($encrypted);

            $this->application = BeneficiaryPersonalDetail::where('application_id', $this->applicationId)->first();

            $this->schemeId = $this->application->scheme_id;

            $this->schemeName = Scheme::where('id', $this->schemeId)->value('name');

        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function openActionModal()
    {

        $this->dispatch('hideLoader');
        // $this->dispatch('openBulkActionModal', selectedIds: [$this->application->application_id]);
// dd($this->application);
        $this->dispatch('openBulkActionModal', [
            'selectedIds' => [
                'application_id' => $this->application->application_id,
                'schemeId' => $this->application->scheme_id,
                'entry_type' => $this->application->application_type,
            ]
        ]);
    }

    // #[On('actionPerformedAndRedirect')]
    // public function navigateToTablePage()
    // {

    //     session()->flash('success', 'The application has been successfully processed.');
    //     return redirect()->route('submitted-list');
    // }
    #[On('actionPerformedAndRedirect')]
    public function navigateToTablePage()
    {
        session()->flash('success', 'The application has been successfully processed.');

        return redirect()->route('lb-application-list', [
            'scheme_id' => Crypt::encryptString($this->schemeId)
        ]);
    }


    public function render()
    {
        return view('livewire.process-application.draft-application-view');
    }
}
