<?php

namespace App\View\Components\ApllicantModal;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\DraftBeneficiaryPersonal;

class ContactDetails extends Component
{
    /**
     * Create a new component instance.
     */
    public $id, $applicantDet, $distname, $ps, $blockmunicorp, $gpward, $villtown, $houseno, $po, $pin;
    public function __construct($id)
    {
        $applicantDet = DraftBeneficiaryPersonal::with('contact')->where('application_id', $id)->first();
        $this->distname = $applicantDet->contact->district->name;
        $this->ps = $applicantDet->contact->police_station;
        if ($applicantDet->rural_urban_id == 1) {
            $this->blockmunicorp = $applicantDet->contact->municipality->name;
            $this->gpward = $applicantDet->contact->ward->name;
        } else {
            $this->blockmunicorp = $applicantDet->contact->block->name;
            $this->gpward = $applicantDet->contact->panchayat->name;
        }
        $this->villtown = $applicantDet->contact->village_town_city;
        $this->houseno = $applicantDet->contact->house_premise_no;
        $this->po = $applicantDet->contact->post_office;
        $this->pin = $applicantDet->contact->pincode;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.apllicant-modal.contact-details');
    }
}
