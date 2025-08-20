<?php

namespace App\View\Components\ApllicantModal;

use Closure;
use Illuminate\View\Component;
use App\Models\BeneficiaryPersonal;
use Illuminate\Contracts\View\View;
use App\Models\DraftBeneficiaryPersonal;

class ContactDetails extends Component
{
    /**
     * Create a new component instance.
     */
    public $id, $applicantDet,$reportType, $distname, $ps, $blockmunicorp, $gpward, $villtown, $houseno, $po, $pin;
    public function __construct($id,$reportType = null)
    {

        //  $reportType = request()->query('reportType');

         if ($reportType === '3') {
            $this->applicantDet = BeneficiaryPersonal::with('contact')
                ->where('beneficiary_id', $id)
                ->firstOrFail();
        } else {
            $this->applicantDet = DraftBeneficiaryPersonal::with('contact')
                ->where('application_id', $id)
                ->firstOrFail();
        }

        $this->distname = $this->applicantDet->contact->district->name;
        $this->ps = $this->applicantDet->contact->police_station;
        if ($this->applicantDet->contact->rural_urban_id == 1) {
            $this->blockmunicorp = $this->applicantDet->contact->municipality->name;
            $this->gpward = $this->applicantDet->contact->ward->name;
        } else {
            $this->blockmunicorp = $this->applicantDet->contact->block->name;
            $this->gpward = $this->applicantDet->contact->panchayat->name;
        }
        $this->villtown = $this->applicantDet->contact->village_town_city;
        $this->houseno = $this->applicantDet->contact->house_premise_no;
        $this->po = $this->applicantDet->contact->post_office;
        $this->pin = $this->applicantDet->contact->pincode;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.apllicant-modal.contact-details');
    }
}
