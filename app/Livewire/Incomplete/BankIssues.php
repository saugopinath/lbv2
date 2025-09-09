<?php

namespace App\Livewire\Incomplete;

use Livewire\Component;

class BankIssues extends Component
{
    public $bankIssues = [];
    public $formData = [];
    public $ifscode, $bankname, $bankbranchname,$new_bank_account;

    public function mount($bankIssues)
    {
        $this->bankIssues = $bankIssues;
        if (!empty($bankIssues)) {
            $this->ifscode = $bankIssues[0]->old_value['ifsc'] ?? '';
            $this->bankname = $bankIssues[0]->old_value['bank_name'] ?? '';
            $this->bankbranchname = $bankIssues[0]->old_value['branch_name'] ?? '';
            $this->new_bank_account = $bankIssues[0]->old_value['account_number'] ?? '';
        }
    }

    public function updatedIfscode()
    {
        $ifs = \App\Models\Ifsccodemaster::with('bank')
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

    public function render()
    {
        return view('livewire.incomplete.bank-issues');
    }
}
