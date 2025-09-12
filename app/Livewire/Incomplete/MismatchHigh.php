<?php

namespace App\Livewire\Incomplete;

use Livewire\Component;
use App\Models\Ifsccodemaster;

class MismatchHigh extends Component
{
    public $application_id, $beneficiary_name, $mobile_no, $father_name;
    public $ifscode, $bankname, $bankbranchname, $new_bank_account;
    public $name_as_in_portal, $name_response_for_bank, $name_matching_score;
    public $old;
    public $dupAction = null;
    public $item;
    // public $getdata;
    public $bank_action = '';
    protected $listeners = [
        'dup-bank-action-changed' => 'setDupAction'
    ];




    public function mount($item)
    {
        $old = $item->old_value ?? [];
        $this->ifscode = $old['ifsc'] ?? '';
        $this->bankname = $old['bank_name'] ?? '';
        $this->bankbranchname = $old['branch_name'] ?? '';
        $this->new_bank_account = $old['account_number'] ?? '';
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

    public function updated()
    {
        $data = [

            'ifscode' => $this->ifscode,
            'new_bank_account' => $this->new_bank_account,
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
        return view('livewire.incomplete.mismatch-high');
    }
}
