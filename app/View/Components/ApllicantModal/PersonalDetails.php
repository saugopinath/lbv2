<?php

namespace App\View\Components\ApllicantModal;

use Closure;
use App\Models\Codemaster;
use Illuminate\Support\Carbon;
use Illuminate\View\Component;
use App\Models\BeneficiaryPersonal;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Crypt;
use App\Models\DraftBeneficiaryPersonal;

class PersonalDetails extends Component
{
    /**
     * Create a new component instance.
     */
    public $id, $applicantDet, $decryptedAadhaar, $dsregno, $dsdate, $mobile, $email,
        $fname, $dob, $age, $ffname, $mfname, $sfname, $caste, $cascerno, $currentDate, $mode,$name;

    public function __construct($id, $reportType = null, $mode = null)
    {
        if (request()->query('reportType')) {
            $reportType = request()->query('reportType');
        }
        // dd($reportType);
        $this->mode = $mode;
        $this->currentDate = Carbon::now()->format('d/m/Y');

        if ($reportType == '3') {
            // dd($id);
            $applicantDet = BeneficiaryPersonal::with(['aadhaar', 'relationships'])->where('application_id', $id)->first();
            // dd($applicantDet );
        } else {
            // dd('ok2');
            $applicantDet = DraftBeneficiaryPersonal::with(['aadhaar', 'relationships'])->where('application_id', $id)->first();
        }
        // dd($applicantDet);
        $this->decryptedAadhaar = Crypt::decryptString($applicantDet->aadhaar->encoded_aadhar);
        $this->dsregno = $applicantDet->ds_registration_no;
        $this->dsdate = Carbon::parse($applicantDet->ds_date)->format('d-m-Y');
        $this->name = $applicantDet->full_name;
        $this->mobile = $applicantDet->mobile_no;
        $this->email = $applicantDet->email;
        $this->fname = $applicantDet->full_name;
        $this->dob = Carbon::parse($applicantDet->dob)->format('d-m-Y');
        $this->age = Carbon::parse($applicantDet->dob)->age;
        // $this->ffname = $applicantDet->relationships->firstWhere('relation_type_id', Codemaster::getIdByCode(131))->full_name;
        // $this->mfname = $applicantDet->relationships->firstWhere('relation_type_id', Codemaster::getIdByCode(132))->full_name;
        $this->ffname = optional(
            $applicantDet->relationships->firstWhere('relation_type_id', Codemaster::getIdByCode(131))
        )->full_name ?? '';

        $this->mfname = optional(
            $applicantDet->relationships->firstWhere('relation_type_id', Codemaster::getIdByCode(132))
        )->full_name ?? '';

        if ($applicantDet->marital_status == Codemaster::getIdByCode(32) || $applicantDet->marital_status == Codemaster::getIdByCode(34)) {
            // $this->sfname = $applicantDet->relationships->firstWhere('relation_type_id', Codemaster::getIdByCode(133))->full_name;
            $this->sfname = optional(
                $applicantDet->relationships->firstWhere(
                    'relation_type_id',
                    Codemaster::getIdByCode(133)
                )
            )->full_name;
        }
        $this->caste = Codemaster::find($applicantDet->caste)->name;
        $this->cascerno = $applicantDet->caste_certificate_no;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        if ($this->mode === 'page') {
            return view('components.apllicant-modal.personal-details-page');
        }
        return view('components.apllicant-modal.personal-details');
    }
}
