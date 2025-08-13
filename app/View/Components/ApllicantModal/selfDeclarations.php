<?php

namespace App\View\Components\apllicantModal;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\DraftBeneficiaryPersonal;

class selfDeclarations extends Component
{
    /**
     * Create a new component instance.
     */
    public $id, $applicantDet, $resident, $no_govt_salary, $info_true, $aadhaar_consent;

    public function __construct($id)
    {
        $applicantDet = DraftBeneficiaryPersonal::with('declaration')->where('application_id', $id)->first();
        $this->resident = $applicantDet->declaration->is_resident;
        $this->no_govt_salary = $applicantDet->declaration->earn_monthly_remuneration;
        $this->info_true = $applicantDet->declaration->info_genuine_decl;
        $this->aadhaar_consent = $applicantDet->declaration->av_status;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.apllicant-modal.self-declarations');
    }
}
