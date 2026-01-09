<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\BeneficiaryPersonal;
use Illuminate\Support\Facades\Crypt;
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
        // dd('ok');
        $realId = Crypt::decrypt($id);
        // dd($realId);
        $this->reportType = request()->query('reportType');
        $this->is_duplicate   = $is_duplicate;
// dd( $this->reportType);
        if ($this->reportType === '3') {
            // $this->application = BeneficiaryPersonal::findOrFail($realId);
             $this->application = BeneficiaryPersonal::where('application_id', $realId)->first();
            //  dd($this->application);
            $this->label = 'Beneficiary Id';
            $this->value = $this->application->beneficiary_id;
            $this->passId = $this->application->application_id;
        } else {
            // $this->application = DraftBeneficiaryPersonal::findOrFail($realId);
            $this->application = DraftBeneficiaryPersonal::where('application_id', $realId)->first();
            // dd( $this->application );
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
