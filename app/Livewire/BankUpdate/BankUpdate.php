<?php

namespace App\Livewire\BankUpdate;

use Livewire\Component;
use App\Models\Ifsccodemaster;
use App\Models\BeneficiaryCommonList;

class BankUpdate extends Component
{
    public $ifscode, $bankname, $bankbranchname, $bank_account_number, $confirmbankaccountnumber;
    public $application_id;
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

    public function mount($application_id)
    {

        $this->application_id = $application_id;

        $query = BeneficiaryCommonList::with(['sourceable', 'sourceable.bank'])
            ->where('sourceable_id', $this->application_id)
            ->first();

        $this->ifscode = $query?->sourceable->bank->ifsc ?? '';
        $this->bank_account_number = $query?->sourceable->bank->bank_account_number ?? '';
        $this->confirmbankaccountnumber = $query?->sourceable->bank->bank_account_number ?? '';

        if ($query && $query->sourceable?->bank) {
            $this->updatedIfscode();
        }
    }

    public function render()
    {
        return view('livewire.bank-update.bank-update');
    }
}
