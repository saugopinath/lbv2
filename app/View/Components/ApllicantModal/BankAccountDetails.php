<?php

namespace App\View\Components\ApllicantModal;

use Closure;
use App\Models\Ifsccodemaster;
use Illuminate\View\Component;
use App\Models\BeneficiaryPersonal;
use Illuminate\Contracts\View\View;
use App\Models\DraftBeneficiaryPersonal;

class BankAccountDetails extends Component
{
    /**
     * Create a new component instance.
     */
    public $id, $applicantDet, $bankname, $bankbranchname, $bankaccountnumber, $ifscode;
    public function __construct($id)
    {

        $reportType = request()->query('reportType');

         if ($reportType === '3') {
            $this->applicantDet = BeneficiaryPersonal::with('bank')
                ->where('beneficiary_id', $id)
                ->first();
        } else {
            $this->applicantDet = DraftBeneficiaryPersonal::with('bank')
                ->where('application_id', $id)
                ->first();
        }
        $this->bankname = $this->applicantDet->bank->ifscbranch->bank->name;
        $this->ifscode = $this->applicantDet->bank->ifsc;
        $this->bankbranchname = $this->applicantDet->bank->ifscbranch->branch;
        $this->bankaccountnumber = $this->applicantDet->bank->bank_account_number;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.apllicant-modal.bank-account-details');
    }
}
