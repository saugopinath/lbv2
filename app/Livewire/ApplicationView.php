<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\BeneficiaryPersonal;
use App\Models\DraftBeneficiaryPersonal;

class ApplicationView extends Component
{
    public $application;
    public $reportType;
    public $label;
    public $value;
    public $passId;

    public function mount($id)
    {
        $this->reportType = request()->query('reportType');

        if ($this->reportType === '3') {
            $this->application = BeneficiaryPersonal::findOrFail($id);
            $this->label = 'Beneficiary Id';
            $this->value = $this->application->beneficiary_id;
            $this->passId = $this->application->beneficiary_id;
        } else {
            $this->application = DraftBeneficiaryPersonal::findOrFail($id);
            $this->label = 'Application Id';
            $this->value = $this->application->application_id;
            $this->passId = $this->application->application_id;
        }
    }

    public function render()
    {
        return view('livewire.application-view');
    }
}
