<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Session;
use Livewire\Component;
use App\Models\Ifsccodemaster;
use App\Models\DraftBeneficiaryPersonal;
use App\Models\DraftBeneficiaryBank;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\BeneficiaryCommonList;
class BankDetails extends Component
{
    public $mode, $application_id;
    public $ifscode, $bankname, $bankbranchname, $bankaccountnumber, $confirmbankaccountnumber, $score, $passbook_name, $scoreColor;
    public function checkScore()
    {
        $app_det = DraftBeneficiaryPersonal::find($this->application_id);
        $result = DB::selectOne("
        SELECT similarity(?, ?) * 100 as score
    ", [$app_det->full_name, $this->passbook_name]);
        $score = (int) $result->score;
        $this->score = $score;
        if ($this->score > 90) {
            $this->scoreColor = 'text-green-600';
        } elseif ($this->score > 40) {
            $this->scoreColor = 'text-blue-600';
        } else {
            $this->scoreColor = 'text-red-600';
        }
    }
    public function updatedIfscode()
    {
        $this->resetErrorBag('ifscode');
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
            $this->addError('ifscode', 'This IFSC code is not registered in our portal.');
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
                $this->passbook_name = $app_det->bank->bankpassbook_name;
                $this->score = $app_det->bank->app_gen_score;
            }
        }
    }
    public function rules()
    {
        return [
            'passbook_name' => 'required|string',
            'score' => 'required|numeric',
            'ifscode' => 'required|string|max:11',
            'bankaccountnumber' => 'required|numeric',
            'confirmbankaccountnumber' => 'required|same:bankaccountnumber',
        ];
    }
    public function messages()
    {
        return [
            'passbook_name.*' => 'Please enter name and check score.',
            'score.*' => 'Score is rquired.',
            'ifscode.*' => 'Please enter a valid IFSC code (maximum 11 characters).',
            'bankaccountnumber.*' => 'Please enter a valid bank account number.',
            'confirmbankaccountnumber.*' => 'The confirmation account number must match the bank account number.',
        ];
    }
    public function save()
    {
        try {
            $validated = $this->validate($this->rules());
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('hideLoader');
            throw $e;
        }
        $duplicate = BeneficiaryCommonList::where('bank_account_number', $validated['bankaccountnumber'])
            ->where('sourceable_id', '!=', $this->application_id)
            ->where('is_reject', false)
            ->exists();
        if ($duplicate) {
            $this->dispatch('hideLoader');
            $this->addError('bankaccountnumber', 'This bank account number is already registered in the portal.');
            return;
        }
        $DraftBeneficiaryBank = DraftBeneficiaryBank::find($this->application_id);
        DB::beginTransaction();
        try {
            if ($this->mode === null && empty($DraftBeneficiaryBank)) {
                $application_id = $this->application_id;
                $DraftBeneficiaryBank = new DraftBeneficiaryBank;
                $DraftBeneficiaryBank->application_id = $application_id;
                $DraftBeneficiaryBank->created_by = Auth::id();
                $DraftBeneficiaryBank->ifsc = $validated['ifscode'];
                $DraftBeneficiaryBank->app_gen_score = $validated['score'];
                $DraftBeneficiaryBank->bankpassbook_name = $validated['passbook_name'];
                $DraftBeneficiaryBank->bank_account_number = $validated['bankaccountnumber'];
                $DraftBeneficiaryBank->save();
                $this->dispatch('bankDet', [
                    'message' => "Bank Details saved successfully for the application id: {$this->application_id}"
                ]);
            } else {
                $DraftBeneficiaryBank->created_by = Auth::id();
                $DraftBeneficiaryBank->ifsc = $validated['ifscode'];
                $DraftBeneficiaryBank->app_gen_score = $validated['score'];
                $DraftBeneficiaryBank->bankpassbook_name = $validated['passbook_name'];
                $DraftBeneficiaryBank->bank_account_number = $validated['bankaccountnumber'];
                $DraftBeneficiaryBank->save();
                $this->dispatch('bankDet', [
                    'message' => "Bank Details updated successfully for the application id: {$this->application_id}"
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('hideLoader');
            throw $e;
        }
        $this->dispatch('hideLoader');
    }
    public function render()
    {
        return view('livewire.bank-details');
    }
}
