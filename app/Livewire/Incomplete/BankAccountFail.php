<?php

namespace App\Livewire\Incomplete;

use Livewire\Component;
use App\Models\Ifsccodemaster;
use App\Models\BeneficiaryPersonal;
use App\Models\ApplicantIncompletDeatil;

class BankAccountFail extends Component
{
    public $ifscode, $bankname, $bankbranchname, $bank_account_number, $old, $dupAction = null, $item, $bank_action = '', $confirmbankaccountnumber;

    protected $listeners = [
        'dup-bank-action-changed' => 'setDupAction'
    ];

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

    // public function mount($item)
    // {
    //     $this->item = $item;
    //     $old = $item->old_value ?? [];

    //      $app_det = ApplicantIncompletDeatil::with('banks')->where('application_id', $item->application_id)->first();
    //     if ($app_det->banks) {
    //         $this->ifscode = $app_det->banks->ifsc;
    //         $this->updatedIfscode($this->ifscode);
    //         $this->bankname;
    //         $this->bankbranchname;
    //     }

    //     $this->ifscode = $old['ifsc'] ?? '';
    //     $this->bank_account_number = $old['bank_account_number'] ?? '';
    // }
    public function mount($item)
    {
        $this->item = $item;

        $old_value = $item->old_value ?? [];
        $new_value = $item->new_value ?? [];

        // $this->bank_action = (string) ($item->change_type ?? '');
        if (old('bank_action') !== null) {
            $this->bank_action = (string) old('bank_action');
        } else {
            $this->bank_action = (string) ($item->change_type ?? '');
        }

        // if (in_array($this->bank_action, ['1', '2', '3'])) {
        //     $this->ifscode = $new_value['ifscode'] ?? '';
        //     $this->bank_account_number = $new_value['bank_account_number'] ?? '';
        // } else {
        //     $this->ifscode = $old_value['ifsc'] ?? '';
        //     $this->bank_account_number = $old_value['bank_account_number'] ?? '';
        // }
        //  if (in_array($this->bank_action, ['1', '2', '3'])) {
        //     $this->ifscode = $new_value['ifscode'] ?? '';
        //     $this->bank_account_number = $new_value['bank_account_number'] ?? '';
        //     $this->confirmbankaccountnumber = $new_value['confirmbankaccountnumber'] ?? '';
        // } else {
        //     $this->ifscode = $old_value['ifsc'] ?? '';
        //     $this->bank_account_number = $old_value['bank_account_number'] ?? '';
        //     $this->confirmbankaccountnumber = $old_value['confirmbankaccountnumber'] ?? '';
        // }

        if (in_array($this->bank_action, ['1', '2', '3'])) {
            $this->ifscode = old('ifscode', $new_value['ifscode'] ?? '');
            $this->bank_account_number = old('bank_account_number', $new_value['bank_account_number'] ?? '');
            $this->confirmbankaccountnumber = old('confirmbankaccountnumber', $new_value['confirmbankaccountnumber'] ?? '');
        } else {
            $this->ifscode = old('ifscode', $old_value['ifsc'] ?? '');
            $this->bank_account_number = old('bank_account_number', $old_value['bank_account_number'] ?? '');
            $this->confirmbankaccountnumber = old('confirmbankaccountnumber', $old_value['confirmbankaccountnumber'] ?? '');
        }

        $app_det = ApplicantIncompletDeatil::with('banks')
            ->where('application_id', $item->application_id)
            ->first();

        if ($app_det && $app_det->banks) {
            $this->updatedIfscode($this->ifscode);
        }
    }

    public function updated()
    {
        // $data = [
        //     'ifscode' => $this->ifscode,
        //     'bank_account_number' => $this->bank_account_number,
        //     'bank_action' => $this->bank_action,
        // ];
        $data = [
            'ifscode' => $this->ifscode,
            'bank_account_number' => $this->bank_account_number,
            'bank_action' => $this->bank_action,
            'confirmbankaccountnumber' => $this->confirmbankaccountnumber,
        ];

        $this->dispatch('trigger-update', $data);
    }
    public function setDupAction($value)
    {
        $this->dupAction = $value;
        $this->bank_action = $value;
    }
    public function updatedBankAction($value)
    {
        // dd($value);
        if ($value == 3) {
            $this->dispatch('hideLoader');
        }
    }
    public function render()
    {
        return view('livewire.incomplete.bank-account-fail');
    }
}
