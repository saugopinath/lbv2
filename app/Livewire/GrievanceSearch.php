<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class GrievanceSearch extends Component
{
    public $selectedOption;
    public $inputValue = '';
    public $grievanceId = '';

    // Define all available search options
    public $searchOptions = [
        'application_id'      => 'Application ID',
        'beneficiary_name'    => 'Beneficiary Name',
        'mobile_number'       => 'Mobile Number',
        'aadhaar_number'      => 'Aadhaar Number',
        'bank_account_number' => 'Bank Account Number',
    ];

    // #[On('searchTriggered')]
    // public function handleSearchTriggered($selectedOption, $inputValue)
    // {
    //     $this->selectedOption = $selectedOption;
    //     $this->inputValue = $inputValue;
    // }

    public function mount(
        $applicationId = null,
        $beneficiaryName = null,
        $mobileNumber = null,
        $aadhaarNumber = null,
        $bankAccountNumber = null,
        $grievanceId = ''
    ) {
        $this->grievanceId = $grievanceId;
        foreach ($this->searchOptions as $key => $label) {
            $camelKey = \Illuminate\Support\Str::camel($key);

            if (!empty($$camelKey)) {
                $this->selectedOption = $label;
                $this->inputValue = $$camelKey;
                break;
            }
        }

        // Default fallback
        if (empty($this->selectedOption)) {
            $this->selectedOption = 'Application ID';
        }
    }

    public function render()
    {
        return view('livewire.grievance-search', [
            'searchOptions' => $this->searchOptions,
        ]);
    }
}
