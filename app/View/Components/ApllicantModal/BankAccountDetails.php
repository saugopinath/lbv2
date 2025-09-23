<?php

namespace App\View\Components\ApllicantModal;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\DraftBeneficiaryPersonal;
use App\Models\Ifsccodemaster;

class BankAccountDetails extends Component
{
    /**
     * Create a new component instance.
     */
    public $id, $applicantDet, $bankname, $bankbranchname, $bankaccountnumber, $ifscode, $mode;
    public function __construct($id, $mode = null)
    {
        $this->mode = $mode;
        $applicantDet = DraftBeneficiaryPersonal::with('bank')->where('application_id', $id)->first();
        $this->bankname = $applicantDet->bank->ifscbranch->bankmaster->name;
        $this->ifscode = $applicantDet->bank->ifsc;
        $this->bankbranchname = $applicantDet->bank->ifscbranch->branch;
        $this->bankaccountnumber = $applicantDet->bank->bank_account_number;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {

        if ($this->mode === 'page') {
            return view('components.apllicant-modal.bank-account-details-page');
        }
        return view('components.apllicant-modal.bank-account-details');
    }
}
