<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Session;
use Livewire\Component;
use App\Models\Ifsccodemaster;
use App\Models\DraftBeneficiaryPersonal;
use App\Models\DraftBeneficiaryBank;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BankDetails extends Component
{
    public $mode, $application_id;
    public $ifscode, $bankname, $bankbranchname, $bankaccountnumber, $confirmbankaccountnumber;
    public function updatedIfscode()
    {
        $ifs = Ifsccodemaster::with('bankmaster')
            ->where('code', $this->ifscode)
            ->where('is_active', 1)
            ->first();

        if ($ifs) {
            $this->bankname = $ifs->bankmaster->name;
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
        return [
            'ifscode' => 'required|string|max:11',
            'bankaccountnumber' => 'required|numeric',
            'confirmbankaccountnumber' => 'required|same:bankaccountnumber',
        ];
    }
    public function messages()
    {
        return [
            'ifscode.*' => 'Please enter a valid IFSC code (maximum 11 characters).',
            'bankaccountnumber.*' => 'Please enter a valid bank account number.',
            'confirmbankaccountnumber.*' => 'The confirmation account number must match the bank account number.',
        ];
    }
    public function save()
    {
        $validated = $this->validate($this->rules());
        $app_det = DraftBeneficiaryBank::where('application_id', $this->application_id)->first();
        DB::beginTransaction();
        try {
            if ($this->mode === null && empty($app_det)) {
                $application_id = $this->application_id;
                DraftBeneficiaryBank::create([
                    'application_id' => $application_id,
                    'created_by' => Auth::id(),
                    'ifsc' => $validated['ifscode'],
                    'bank_account_number' => $validated['bankaccountnumber'],
                ]);
                $this->dispatch('bankDet', [
                    'message' => "Bank Details saved successfully for the application id: {$this->application_id}"
                ]);
            } else {
                $data = [
                    'ifsc' => $validated['ifscode'],
                    'bank_account_number' => $validated['bankaccountnumber'],
                ];
                DraftBeneficiaryBank::where('application_id', $this->application_id)->update($data);
                $this->dispatch('bankDet', [
                    'message' => "Bank Details updated successfully for the application id: {$this->application_id}"
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    public function render()
    {
        return view('livewire.bank-details');
    }
}
