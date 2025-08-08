<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Ifsccodemaster;
use App\Models\DraftBeneficiaryPersonal;
use App\Models\DraftBeneficiaryBank;
use Illuminate\Support\Facades\Auth;

class BankDetails extends Component
{
    public $mode, $id;
    public $ifscode, $bankname, $bankbranchname, $bankaccountnumber, $confirmbankaccountnumber;
    public function updatedIfscode()
    {
        $ifs = Ifsccodemaster::with('bank')
            ->where('code', $this->ifscode)
            ->where('is_active', 1)
            ->first();

        if ($ifs) {
            $this->bankname = $ifs->bank->name;
            $this->bankbranchname = $ifs->branch;
        } else {
            $this->bankname = '';
            $this->bankbranchname = '';
        }
    }
    public function mount($mode = null, $id = null)
    {
        $this->mode = $mode;
        if ($id != null) {
            $this->id = $id;
            $app_det = DraftBeneficiaryPersonal::with('bank')->where('application_id', $id)->first();
            $this->ifscode = $app_det->bank->ifsc;
            $this->updatedIfscode($this->ifscode);
            $this->bankname;
            $this->bankbranchname;
            $this->bankaccountnumber = trim($app_det->bank->bank_account_number);
            $this->confirmbankaccountnumber = trim($app_det->bank->bank_account_number);
        }
    }
    public function rules()
    {
        $rules = [
            'ifscode' => 'required|string',
            'bankaccountnumber' => 'required|numeric',
            'confirmbankaccountnumber' => 'required|same:bankaccountnumber',
        ];
        return $rules;
    }
    public function save()
    {
        $validated = $this->validate($this->rules());
        if ($this->mode === null) {
            $applicantion = DraftBeneficiaryPersonal::first();
            DraftBeneficiaryBank::create([
                'application_id' => $applicantion->application_id,
                'created_by' => Auth::id(),
                'ifsc' => $validated['ifscode'],
                'bank_account_number' => $validated['bankaccountnumber'],
            ]);
        } else {
            $data = [
                'ifsc' => $validated['ifscode'],
                'bank_account_number' => $validated['bankaccountnumber'],
            ];
            DraftBeneficiaryBank::where('application_id', $this->id)->update($data);
        }
    }
    public function render()
    {
        return view('livewire.bank-details');
    }
}
