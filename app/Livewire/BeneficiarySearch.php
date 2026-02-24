<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class BeneficiarySearch extends Component
{
    public $selectedOption;
    public $inputValue = '';
    public $isApproved;
    public $searchOptions = [
        'application_id'      => 'Application ID',
        'beneficiary_name'    => 'Beneficiary Name',
        'mobile_number'       => 'Mobile Number',
        'aadhaar_number'      => 'Aadhaar Number',
        'bank_account_number' => 'Bank Account Number',
    ];
    
    public function mount($isApproved = false)
    {
        $this->isApproved = $isApproved;
        foreach ($this->searchOptions as $key => $label) {
            $camelKey = \Illuminate\Support\Str::camel($key);
            if (!empty($$camelKey)) {
                $this->selectedOption = $label;
                $this->inputValue = $$camelKey;
                break;
            }
        }
        if (empty($this->selectedOption)) {
            $this->selectedOption = 'Application ID';
        }
    }

    #[On('searchTriggered')]
    public function handleSearch($data)
    {
        switch ($data['key']) {
            case 'application_id':
                break;
            case 'beneficiary_name':
                break;
            case 'mobile_number':
                break;
            case 'aadhaar_number':
                break;
            case 'bank_account_number':
                break;
            default:
        }
    }

    public function render()
    {
        return view('livewire.beneficiary-search', [
            'searchOptions' => $this->searchOptions,
        ]);
    }
}
