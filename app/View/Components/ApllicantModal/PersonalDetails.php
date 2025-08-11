<?php

namespace App\View\Components\ApllicantModal;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Crypt;
use App\Models\DraftBeneficiaryPersonal;

class PersonalDetails extends Component
{
    /**
     * Create a new component instance.
     */
    public $id, $applicant, $decrypted;

    public function __construct($id)
    {
        $this->id = $id;
        $this->applicant = DraftBeneficiaryPersonal::with(['aadhaar', 'relationships'])->where('application_id', $id)->first();
        // dd($this->applicant);
        $this->decrypted = Crypt::decryptString($this->applicant->aadhaar->encoded_aadhar);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.apllicant-modal.personal-details');
    }
}
