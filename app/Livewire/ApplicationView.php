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
    public $is_duplicate;

    public function mount($id,$is_duplicate = 0)
    {
        $this->reportType = request()->query('reportType');
        $this->is_duplicate   = $is_duplicate;

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
