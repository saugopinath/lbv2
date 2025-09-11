<?php

namespace App\Livewire\Incomplete;

use Livewire\Component;
use App\Models\Ifsccodemaster;

class MismatchLow extends Component
{
    public $application_id, $beneficiary_name, $mobile_no, $father_name;
    public $ifscode, $bankname, $bankbranchname, $new_bank_account;
    public $name_as_in_portal, $name_response_for_bank, $name_matching_score;

    public $bank_action = '';
    public $old;
    public $dupAction = null;
    public $item;
    protected $listeners = [
        'dup-bank-action-changed' => 'setDupAction'
    ];

    public function mount($item)
    {
        $old = $item->old_value ?? [];

        $this->application_id = $old['application_id'] ?? '';
        $this->beneficiary_name = $old['beneficiary_name'] ?? '';
        $this->mobile_no = $old['mobile_no'] ?? '';
        $this->father_name = $old['father_name'] ?? '';
        $this->ifscode = $old['ifsc'] ?? '';
        $this->bankname = $old['bank_name'] ?? '';
        $this->bankbranchname = $old['branch_name'] ?? '';
        $this->new_bank_account = $old['account_number'] ?? '';
        $this->name_as_in_portal = $old['name_as_in_portal'] ?? '';
        $this->name_response_for_bank = $old['name_response_for_bank'] ?? '';
        $this->name_matching_score = $old['name_matching_score'] ?? '';
    }

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
    public function setDupAction($value)
    {
        $this->dupAction = $value;
        $this->bank_action = $value;
    }
    public function render()
    {
        return view('livewire.incomplete.mismatch-low');
    }
}
