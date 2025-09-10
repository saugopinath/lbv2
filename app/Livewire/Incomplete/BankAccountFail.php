<?php

namespace App\Livewire\Incomplete;

use Livewire\Component;
use App\Models\Ifsccodemaster;

class BankAccountFail extends Component
{
    public $bankIssues = [];
    public $formData = [];
    public $ifscode, $bankname, $bankbranchname, $new_bank_account;
    public $bank_action = '';
    public $old;

    public $dupAction = null;

    protected $listeners = [
        'dup-bank-action-changed' => 'setDupAction'
    ];

    public function mount($item)
    {
        $this->old = $item;

        $oldData = $item->old_value ?? [];

        $this->ifscode = $oldData['ifsc'] ?? '';
        $this->bankname = $oldData['bank_name'] ?? '';
        $this->bankbranchname = $oldData['branch_name'] ?? '';
        $this->new_bank_account = $oldData['account_number'] ?? '';
    }

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
            $this->bankname = null;
            $this->bankbranchname = null;
        }
    }


    public function setDupAction($value)
    {
        $this->dupAction = $value;
        $this->bank_action = $value;
    }

    public function render()
    {
        return view('livewire.incomplete.bank-account-fail');
    }
}
