<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Codemaster;
use App\Models\BeneficiaryPersonal;
use App\Models\BeneficiaryEnclosure;
use Illuminate\Support\Facades\Crypt;
use App\Models\DraftBeneficiaryPersonal;

class ApplicationView extends Component
{
    public $ffname;
    public $mfname;
    public $application;
    public $decryptedData = null;
    public $showModal = false;

    public function mount($id)
    {
        $reportType = request()->query('reportType');

        if ($reportType === '2') {
            $this->application = DraftBeneficiaryPersonal::findOrFail($id);

        } elseif ($reportType === '3') {
            $this->application = BeneficiaryPersonal::findOrFail($id);
        }
    }

    public function render()
    {
        return view('livewire.application-view');
    }
}
