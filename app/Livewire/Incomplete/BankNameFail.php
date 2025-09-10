<?php

namespace App\Livewire\Incomplete;

use Livewire\Component;

class BankNameFail extends Component
{
    public $old;

    public $dupAction = null;

    public $ifscode, $bankname, $bankbranchname, $new_bank_account;
    public $application_id, $beneficiary_name, $mobile_no, $father_name;
    public $name_as_in_portal, $name_response_for_bank, $name_matching_score;
    public $bank_action = '';

    protected $listeners = [
        'dup-bank-action-changed' => 'setDupAction'
    ];

    public function mount($item, $dupAction = null)
    {
        $this->old = $item;

        $oldData = $item->old_value ?? [];

        $this->ifscode = $oldData['ifsc'] ?? '';
        $this->bankname = $oldData['bank_name'] ?? '';
        $this->bankbranchname = $oldData['branch_name'] ?? '';
        $this->new_bank_account = $oldData['account_number'] ?? '';

        $this->application_id = $oldData['application_id'] ?? '';
        $this->beneficiary_name = $oldData['beneficiary_name'] ?? '';
        $this->mobile_no = $oldData['mobile_no'] ?? '';
        $this->father_name = $oldData['father_name'] ?? '';
        $this->name_as_in_portal = $oldData['name_as_in_portal'] ?? '';
        $this->name_response_for_bank = $oldData['name_response_for_bank'] ?? '';
        $this->name_matching_score = $oldData['name_matching_score'] ?? '';
    }

    public function setDupAction($value)
    {
        $this->dupAction = $value;
        $this->bank_action = $value;
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
        return view('livewire.incomplete.bank-name-fail');
    }
}
