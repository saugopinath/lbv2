<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Session;
use Livewire\Component;
use App\Models\Ifsccodemaster;
use App\Models\DraftBeneficiaryPersonal;
use App\Models\DraftBeneficiaryBank;
use Illuminate\Support\Facades\Auth;

class BankDetails extends Component
{
    public $mode, $application_id;
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
    public function mount($mode = null, $application_id = null)
    {
        $this->mode = $mode;
        if ($application_id != null) {
            $this->application_id = $application_id;
            $app_det = DraftBeneficiaryPersonal::with('bank')->where('application_id', $application_id)->first();
            if ($app_det->bank) {
                $this->ifscode = $app_det->bank->ifsc;
                $this->updatedIfscode($this->ifscode);
                $this->bankname;
                $this->bankbranchname;
                $this->bankaccountnumber = trim($app_det->bank->bank_account_number);
                $this->confirmbankaccountnumber = trim($app_det->bank->bank_account_number);
            }
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
        $app_det = DraftBeneficiaryBank::where('application_id', $this->application_id)->first();
        if ($this->mode === null && empty($app_det)) {
            $application_id = $this->application_id;
            DraftBeneficiaryBank::create([
                'application_id' => $application_id,
                'created_by' => Auth::id(),
                'ifsc' => $validated['ifscode'],
                'bank_account_number' => $validated['bankaccountnumber'],
            ]);
        } else {
            $data = [
                'ifsc' => $validated['ifscode'],
                'bank_account_number' => $validated['bankaccountnumber'],
            ];
            DraftBeneficiaryBank::where('application_id', $this->application_id)->update($data);
        }
        $this->dispatch('bankDet');
    }
    public function render()
    {
        return view('livewire.bank-details');
    }
}
