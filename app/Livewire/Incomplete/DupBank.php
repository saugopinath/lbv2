<?php

namespace App\Livewire\Incomplete;

use App\Models\Ifsccodemaster;
use Livewire\Component;

class DupBank extends Component
{
    public $bankIssues = [];
    public $formData = [];
    public $ifscode, $bankname, $bankbranchname, $new_bank_account;
    public $bank_action = '';
    public $old;
    public $item;

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

    public function updatedBankAction($value)
    {
        $this->dispatch('dup-bank-action-changed', $value);

    }

    public function render()
    {
        return view('livewire.incomplete.dup-bank');
    }
}


