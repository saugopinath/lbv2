<?php

namespace App\View\Components\Incomplete;

use Illuminate\View\Component;

class MismatchHigh extends Component
{
    public $item;
    public $ifscode;
    public $bankname,$name_matching_score;
    public $bankbranchname;
    public $new_bank_account, $application_id, $beneficiary_name, $mobile_no, $father_name, $name_as_in_portal,$name_response_for_bank;

    public function __construct($item)
    {
        $this->item = $item;

        if (!empty($item)) {
            $this->ifscode = $item->old_value['ifsc'] ?? '';
            $this->bankname = $item->old_value['bank_name'] ?? '';
            $this->bankbranchname = $item->old_value['branch_name'] ?? '';
            $this->new_bank_account = $item->old_value['account_number'] ?? '';

            $this->application_id = $item->old_value['application_id'] ?? '';
            $this->beneficiary_name = $item->old_value['beneficiary_name'] ?? '';
            $this->mobile_no = $item->old_value['mobile_no'] ?? '';
            $this->father_name = $item->old_value['father_name'] ?? '';
            $this->name_as_in_portal = $item->old_value['name_as_in_portal'] ?? '';
            $this->name_response_for_bank = $item->old_value['name_response_for_bank'] ?? '';
            $this->name_matching_score = $item->old_value['name_matching_score'] ?? '';
        }
    }

    public function render()
    {
        return view('components.incomplete.mismatch-high');
    }
}
