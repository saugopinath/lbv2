<?php

namespace App\View\Components\ApllicantModal;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Crypt;
use App\Models\DraftBeneficiaryPersonal;
use Illuminate\Support\Carbon;
use App\Models\Codemaster;

class PersonalDetails extends Component
{
    /**
     * Create a new component instance.
     */
    public $id, $applicantDet, $decryptedAadhaar, $dsregno, $dsdate, $mobile, $email,
        $fname, $dob, $age, $ffname, $mfname, $sfname, $caste, $cascerno, $currentDate;

    public function __construct($id)
    {
        $this->currentDate = Carbon::now()->format('d/m/Y');
        $applicantDet = DraftBeneficiaryPersonal::with(['aadhaar', 'relationships'])->where('application_id', $id)->first();
        $this->decryptedAadhaar = Crypt::decryptString($applicantDet->aadhaar->encoded_aadhar);
        $this->dsregno = $applicantDet->ds_registration_no;
        $this->dsdate = Carbon::parse($applicantDet->ds_date)->format('d-m-Y');
        $this->mobile = $applicantDet->mobile_no;
        $this->email = $applicantDet->email;
        $this->fname = $applicantDet->full_name;
        $this->dob = Carbon::parse($applicantDet->dob)->format('d-m-Y');
        $this->age = Carbon::parse($applicantDet->dob)->age;
        $this->ffname = $applicantDet->relationships->firstWhere('relation_type_id', Codemaster::getIdByCode(131))->full_name;
        $this->mfname = $applicantDet->relationships->firstWhere('relation_type_id', Codemaster::getIdByCode(132))->full_name;
        if ($applicantDet->marital_status == Codemaster::getIdByCode(32) || $applicantDet->marital_status == Codemaster::getIdByCode(34)) {
            $this->sfname = $applicantDet->relationships->firstWhere('relation_type_id', Codemaster::getIdByCode(133))->full_name;
        }
        $this->caste = Codemaster::find($applicantDet->caste)->name;
        $this->cascerno = $applicantDet->caste_certificate_no;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.apllicant-modal.personal-details');
    }
}
