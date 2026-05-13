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
    public $id, $applicantDet, $decryptedAadhaar, $ds_registration_no, $duaresarkarDate, $mobile, $email,
        $fname, $dob, $age, $ben_father_name, $ben_mother_name, $ben_spouse_name, $caste, $caste_cer_no, $currentDate, $mode,$beneficiary_name;

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
        $this->decryptedAadhaar = Crypt::decryptString($applicantDet->aadhaar->encoded_aadhaar);
        $this->ds_registration_no = $applicantDet->ds_registration_no;
        $this->duaresarkarDate = Carbon::parse($applicantDet->ds_date)->format('d-m-Y');
        $this->beneficiary_name = $applicantDet->full_name;
        $this->mobile = $applicantDet->mobile_no;
        $this->email = $applicantDet->email;
        $this->fname = $applicantDet->full_name;
        $this->dob = Carbon::parse($applicantDet->dob)->format('d-m-Y');
        $this->age = Carbon::parse($applicantDet->dob)->age;
        // $this->ffname = $applicantDet->relationships->firstWhere('relation_type_id', Codemaster::getIdByCode(131))->full_name;
        // $this->mfname = $applicantDet->relationships->firstWhere('relation_type_id', Codemaster::getIdByCode(132))->full_name;
        $this->ben_father_name = optional(
            $applicantDet->relationships->firstWhere('relation_type_id', Codemaster::getIdByCode(131))
        )->full_name ?? '';

        $this->ben_mother_name = optional(
            $applicantDet->relationships->firstWhere('relation_type_id', Codemaster::getIdByCode(132))
        )->full_name ?? '';

        if ($applicantDet->marital_status == Codemaster::getIdByCode(32) || $applicantDet->marital_status == Codemaster::getIdByCode(34)) {
            // $this->ben_spouse_name = $applicantDet->relationships->firstWhere('relation_type_id', Codemaster::getIdByCode(133))->full_name;
            $this->ben_spouse_name = optional(
                $applicantDet->relationships->firstWhere(
                    'relation_type_id',
                    Codemaster::getIdByCode(133)
                )
            )->full_name;
        }
        $this->caste = Codemaster::find($applicantDet->caste)->name;
        $this->caste_cer_no = $applicantDet->caste_certificate_no;
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
