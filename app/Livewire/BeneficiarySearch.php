<?php

namespace App\Livewire;

use Livewire\Component;

class BeneficiarySearch extends Component
{
    public $selectedOption = 'application_id';
    public $inputValue = '';
    public $isApproved = false;
    public $fields = [

        'application_id' => [
            'label' => 'Application ID',
            'rules' => 'required|numeric',
            'type'  => 'number'
        ],

        'beneficiary_name' => [
            'label' => 'Beneficiary Name',
            'rules' => 'required|regex:/^[a-zA-Z\s]+$/',
            'type'  => 'text'
        ],

        'mobile_number' => [
            'label' => 'Mobile Number',
            'rules' => 'required|numeric|digits:10',
            'max'   => 10,
            'type'  => 'number'
        ],

        'aadhaar_number' => [
            'label' => 'Aadhaar Number',
            'rules' => 'required|numeric|digits:12',
            'max'   => 12,
            'type'  => 'number'
        ],

        'bank_account_number' => [
            'label' => 'Bank Account Number',
            'rules' => 'required|numeric',
            'type'  => 'number'
        ],

    ];

    public function mount($isApproved = false, $selectedOption = null, $inputValue = null)
    {
        $this->isApproved = $isApproved;
        $this->selectedOption = $selectedOption ?? 'application_id';
        $this->inputValue = $inputValue ?? '';
    }

    private function getValidationRules($key)
    {
        return $this->fields[$key]['rules'] ?? 'required';
    }

    public function search()
    {
        $this->validate([
            'selectedOption' => 'required'
        ], [
            'selectedOption.required' => 'Please select a search criteria.'
        ]);

        $key = $this->selectedOption;

        $fieldLabel = $this->fields[$key]['label'] ?? 'Value';

        $data = $this->validate([
            'inputValue' => $this->getValidationRules($key),
        ], [
            'inputValue.required' => "The $fieldLabel is required.",
            'inputValue.numeric'  => "The $fieldLabel must be numeric.",
            'inputValue.digits'   => "The $fieldLabel must be :digits digits.",
            'inputValue.regex'    => "The $fieldLabel should only contain characters (A-Z, a-z).",
        ]);
        dd($key, $this->inputValue);
    }

    public function render()
    {
        return view('livewire.beneficiary-search');
    }
}
