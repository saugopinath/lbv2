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
    public $moduleCode;

    public function mount()
    {
        try {
            $encrypted = request()->query('application_id');
            $this->applicationId = (int) Crypt::decryptString($encrypted);

            $this->application = BeneficiaryPersonalDetail::where('application_id', $this->applicationId)->first();

            $this->schemeId = $this->application->scheme_id;

            $this->schemeName = Scheme::where('id', $this->schemeId)->value('name');

            $moduleParam = request()->query('module');
            if ($moduleParam) {
                try {
                    $this->moduleCode = Crypt::decryptString($moduleParam);
                } catch (\Exception $e) {
                    $this->moduleCode = $moduleParam;
                }
            }

        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function openActionModal()
    {
        $this->dispatch('hideLoader');

        $this->dispatch('openBulkActionModal', [
            'selectedIds' => [
                'application_id' => $this->application->application_id,
                'schemeId' => $this->application->scheme_id,
                'entry_type' => $this->application->application_type,
                'module_code' => $this->moduleCode,
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

        $params = [
            'scheme_id' => Crypt::encryptString($this->schemeId)
        ];
        if (!empty($this->moduleCode)) {
            $params['module'] = Crypt::encryptString($this->moduleCode);
        }

        return redirect()->route('lb-application-list', $params);
    }


    public function render()
    {
        return view('livewire.process-application.draft-application-view');
    }
}
