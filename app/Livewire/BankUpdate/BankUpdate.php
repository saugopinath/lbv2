<?php

namespace App\Livewire\BankUpdate;

use Livewire\Component;
use App\Models\Ifsccodemaster;
use App\Models\BeneficiaryPersonalDetail;

class BankUpdate extends Component
{
    public $ifscode, $bankname, $bankbranchname, $bank_account_number, $confirmbankaccountnumber;
    public $application_id,$scheme_id;
    public function updatedIfscode()
    {
        $ifs = Ifsccodemaster::with('bank')
            ->where('code', $this->ifscode)
            ->where('is_active', 1)
            ->first();

        if ($ifs) {
            $this->bankname = $ifs->bank->name ?? '';
            $this->bankbranchname = $ifs->branch ?? '';
        } else {
            $this->bankname = '';
            $this->bankbranchname = '';
        }
    }

    public function mount($application_id,$scheme_id)
    {
        $this->application_id = $application_id;
        $this->scheme_id = $scheme_id;
   
        $query = BeneficiaryPersonalDetail::query()
            ->with(['contact', 'banks'])
            ->where('application_id', $this->application_id)
            ->where('scheme_id', $this->scheme_id)
            ->first();
              
        $this->ifscode = $query?->banks?->ifscode ?? '';
        $this->bank_account_number = $query?->banks?->bankaccountnumber ?? '';
        $this->confirmbankaccountnumber = $query?->banks?->bankaccountnumber ?? '';

        if ($query && $query->banks) {
            $this->updatedIfscode();
        }
    }

    public function render()
    {
        return view('livewire.bank-update.bank-update');
    }
}
