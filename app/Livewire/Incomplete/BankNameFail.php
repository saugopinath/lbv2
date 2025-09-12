<?php

namespace App\Livewire\Incomplete;

use Livewire\Component;
use App\Models\Ifsccodemaster;
use App\Models\BeneficiaryPersonal;

class BankNameFail extends Component
{
    public $ifscode, $bankname, $bankbranchname, $bank_account_number, $old, $dupAction = null, $item, $bank_action = '';

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

    public function mount($item)
    {
        $this->item = $item;
        $old = $item->old_value ?? [];

        $app_det = BeneficiaryPersonal::with('bank')->where('application_id', $item->application_id)->first();
        if ($app_det->bank) {
            $this->ifscode = $app_det->bank->ifsc;
            $this->updatedIfscode($this->ifscode);
            $this->bankname;
            $this->bankbranchname;
        }

        $this->ifscode = $old['ifsc'] ?? '';
        $this->bank_account_number = $old['bank_account_number'] ?? '';
    }

    public function updated()
    {
        $data = [
            'ifscode' => $this->ifscode,
            'bank_account_number' => $this->bank_account_number,
            'bank_action' => $this->bank_action,
        ];

        $this->dispatch('trigger-update', $data);
    }
    public function setDupAction($value)
    {
        $this->dupAction = $value;
        $this->bank_action = $value;
    }

    public function render()
    {
        return view('livewire.incomplete.bank-name-fail');
    }
}
