<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Ifsccodemaster;
use App\Models\DraftBeneficiaryPersonal;
use App\Models\DraftBeneficiaryBank;
use Illuminate\Support\Facades\Auth;
class BankDetails extends Component
{
    public $mode;
    public $ifscode, $bankname, $bankbranchname, $bankaccountnumber, $confirmbankaccountnumber;
    public function updatedIfscode()
    {
        $ifs = Ifsccodemaster::with('bank')
            ->where('code', $this->ifscode)
            ->where('is_active', 1)
            ->first();

        if ($ifs && $ifs->bank && $ifs->bank->is_active) {
            $this->bankname = $ifs->bank->name;
            $this->bankbranchname = $ifs->branch;
        } else {
            $this->bankname = '';
            $this->bankbranchname = '';
        }
    }
    public function mount($mode = null)
    {
        $this->mode = $mode;
    }
    public function rules()
    {
        $rules = [
            'ifscode' => 'required|string',
            'bankaccountnumber' => 'required|digits:11|numeric',
            'confirmbankaccountnumber' => 'required|same:bankaccountnumber',
        ];
        return $rules;
    }
    public function save()
    {
        if ($this->mode === null) {
            $validated = $this->validate($this->rules());
            $applicantion = DraftBeneficiaryPersonal::first();
            DraftBeneficiaryBank::create([
                'application_id' => $applicantion->application_id,
                'created_by' => Auth::user()->id,
                'ifsc' => $validated['ifscode'],
                'bank_account_number' => $validated['bankaccountnumber'],
            ]);
        } else {
        }
    }
    public function render()
    {
        return view('livewire.bank-details');
    }
}
