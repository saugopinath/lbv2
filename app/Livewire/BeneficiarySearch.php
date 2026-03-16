<?php

namespace App\Livewire;

use App\Models\BeneficiaryPersonalDetail;
use Illuminate\Support\Str;
use Livewire\Component;
use App\Models\Scheme;
use Illuminate\Support\Facades\Crypt;

class BeneficiarySearch extends Component
{
    public $selectedOption = null;
    public $inputValue = '';
    public $isApproved = false;
    public $displayType = 'select';
    public $isFinal = false;
    public $isAssigned = false;
    public $schemes = [];
    public $isShownScheme = true;
    public $selectedScheme = null;
    public $fields = [
        'application_id' => [
            'label' => 'Application ID',
            'rules' => 'required|numeric',
            'type'  => 'number',
            'input_type' => 'text'
        ],
        'beneficiary_name' => [
            'label' => 'Beneficiary Name',
            'rules' => 'required|regex:/^[a-zA-Z\s]+$/',
            'type'  => 'text',
            'input_type' => 'text'
        ],
        'mobile_number' => [
            'label' => 'Mobile Number',
            'rules' => 'required|numeric|digits:10',
            'max'   => 10,
            'type'  => 'number',
            'input_type' => 'text'
        ],
        'aadhaar_number' => [
            'label' => 'Aadhaar Number',
            'rules' => 'required|numeric|digits:12',
            'max'   => 12,
            'type'  => 'number',
            'input_type' => 'password'
        ],
        'bank_account_number' => [
            'label' => 'Bank Account Number',
            'rules' => 'required|numeric',
            'type'  => 'number',
            'input_type' => 'text'
        ],
    ];

    public function mount($isApproved = false, $selectedOption = null, $inputValue = null, $displayType = 'select', $isFinal = false, $isAssigned = false, $isShownScheme = true)
    {
        $this->isApproved = $isApproved;
        $this->selectedOption = $selectedOption;
        $this->inputValue = $inputValue ?? '';
        $this->displayType = $displayType;
        $this->isShownScheme = $isShownScheme;
        $scheme_id = null;
        if ($isAssigned) {
            $select_lgd = session('lgd_session');

            if (!empty($select_lgd['scheme_id'])) {
                $scheme_id = Crypt::decryptString($select_lgd['scheme_id']);
            }
        }
        $query = Scheme::query()
            ->where('is_active', 1)
            ->when($scheme_id, fn($q) => $q->where('id', $scheme_id));
        if ($isFinal) {
            $query->whereHas('schemeFinalSubmitChecks', function ($q) {
                $q->where('is_final_submitted', true);
            });
        }
        $this->schemes = $query->get();
    }

    private function getValidationRules($key)
    {
        return $this->fields[$key]['rules'] ?? 'required';
    }
    public function updatedSelectedOption()
    {
        $this->resetValidation();
    }
    public function search()
    {
        $rules = [];
        $messages = [];
        $rules['selectedOption'] = 'required';
        $messages['selectedOption.required'] = 'Please select a search criteria.';
        if ($this->isShownScheme) {
            $rules['selectedScheme'] = 'required';
            $messages['selectedScheme.required'] = 'Please select a scheme.';
        }
        $this->validate($rules, $messages);
        $key = $this->selectedOption;
        $fieldLabel = $this->fields[$key]['label'] ?? 'Value';

        $this->validate([
            'inputValue' => $this->getValidationRules($key),
        ], [
            'inputValue.required' => "The $fieldLabel is required.",
            'inputValue.numeric'  => "The $fieldLabel must be numeric.",
            'inputValue.digits'   => "The $fieldLabel must be :digits digits.",
            'inputValue.regex'    => "The $fieldLabel should only contain characters (A-Z, a-z).",
        ]);

        $payload = [
            'searchKey'   => $key,
            'searchValue' => $this->inputValue,
            'isApproved'  => $this->isApproved,
            'schemeId'    => $this->selectedScheme,
        ];
        dd($payload);
        $this->dispatch('beneficiary-search', data: $payload);
    }

    public function render()
    {
        return view('livewire.beneficiary-search');
    }
}
