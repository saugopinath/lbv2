<?php

namespace App\Livewire;

use App\Models\BeneficiaryPersonalDetail;
use Illuminate\Support\Str;
use Livewire\Component;
use App\Models\Scheme;
use App\Models\WorkflowsteproleMapping;
use Illuminate\Support\Facades\Crypt;
use App\Services\WorkflowService;

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
    public $lgdData = [];
    public $fields = [
        'application_id' => [
            'label' => 'Application ID',
            'rules' => 'required|numeric',
            'type'  => 'number',
            'input_type' => 'text'
        ],
        'beneficiary_id' => [
            'label' => 'Beneficiary ID',
            'rules' => 'required|numeric',
            'type'  => 'number',
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
        'beneficiary_name' => [
            'label' => 'Beneficiary Name',
            'rules' => 'required|regex:/^[a-zA-Z\s]+$/',
            'type'  => 'text',
            'input_type' => 'text'
        ],
        'bank_account_number' => [
            'label' => 'Bank Account Number',
            'rules' => 'required|numeric',
            'type'  => 'number',
            'input_type' => 'text'
        ],
    ];

    public function mount(
        $isApproved = false,
        $selectedOption = null,
        $inputValue = null,
        $displayType = 'select',
        $isFinal = false,
        $isAssigned = false,
        $isShownScheme = true,
        $excludeFields = []
    ) {
        $this->isApproved = $isApproved;
        $this->selectedOption = $selectedOption;
        $this->inputValue = $inputValue ?? '';
        $this->displayType = $displayType;
        $this->isShownScheme = $isShownScheme;
        $scheme_id = null;
        $select_lgd = session('lgd_session');
        if ($select_lgd) {
            if (!empty($select_lgd['district_id'])) {
                $this->lgdData['created_by_dist_code'] = Crypt::decryptString($select_lgd['district_id']);
            }
            if (!empty($select_lgd['block_id'])) {
                $this->lgdData['created_by_local_body_code'] = Crypt::decryptString($select_lgd['block_id']);
            }
            if (!empty($select_lgd['subdivision_id'])) {
                $this->lgdData['created_by_local_body_code'] = Crypt::decryptString($select_lgd['subdivision_id']);
            }
        }
        if ($isAssigned) {
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
        if (!empty($excludeFields)) {
            $this->fields = array_diff_key($this->fields, array_flip($excludeFields));
        }
    }

    private function getValidationRules($key)
    {
        return $this->fields[$key]['rules'] ?? 'required';
    }

    public function search(WorkflowService $workflowService)
    {
        $rules = [];
        $messages = [];
        $rules['selectedOption'] = 'required';
        $messages['selectedOption.required'] = 'Please select a search criteria.';
        if ($this->isShownScheme) {
            $rules['selectedScheme'] = 'required';
            $messages['selectedScheme.required'] = 'Please select a scheme.';
        } else {
            $this->selectedScheme = Scheme::where('is_active', 1)->first()->id;
        }
        if ($this->selectedOption) {
            $key = $this->selectedOption;
            $fieldLabel = $this->fields[$key]['label'] ?? 'Value';
            $rules['inputValue'] = $this->getValidationRules($key);
            $messages['inputValue.required'] = "The $fieldLabel is required.";
            $messages['inputValue.numeric']  = "The $fieldLabel must be numeric.";
            $messages['inputValue.digits']   = "The $fieldLabel must be :digits digits.";
            $messages['inputValue.regex']    = "The $fieldLabel should only contain characters (A-Z, a-z).";
        }
        $this->validate($rules, $messages);
        $modelClass = BeneficiaryPersonalDetail::class;
        $searchValue = $this->inputValue;
        $query = $modelClass::query()->whereIn('is_clean', [1, 2]);
        $query->where('scheme_id', $this->selectedScheme);
        if ($this->lgdData) {
            $query->where($this->lgdData);
        }
        if ($this->isApproved) {
            $getMinMaxWorkflowStep = WorkflowsteproleMapping::getMinMaxWorkflowStep($this->selectedScheme);
            $nextLabelRoleId = $workflowService->getLabelRoles($this->selectedScheme, $getMinMaxWorkflowStep['max'])->next_label_role_id;
            $query->where('is_final', 1);
            $query->where('next_level_role_id', $nextLabelRoleId);
        }
        switch ($key) {
            case 'application_id':
                $query->where($key, $searchValue);
                break;
            case 'beneficiary_id':
                $query->where($key, $searchValue);
                break;
            case 'beneficiary_name':
                $query->where($key, $searchValue);
                break;
            case 'mobile_number':
                $query->where('other_details->mobile_no', $searchValue);
                break;
            case 'aadhaar_number':
                $query->whereHas('aadhar', function ($q) use ($searchValue) {
                    $q->where('aadhar_vault', md5($searchValue));
                });
                break;
            case 'bank_account_number':
                $query->whereHas('bank', function ($q) use ($searchValue) {
                    $q->where('bankaccountnumber', $searchValue);
                });
                break;
        }
        // dd($query->toSql());
        $results = $query->get(['application_id', 'beneficiary_id', 'next_level_role_id', 'is_final', 'is_clean', 'scheme_id']);
        $payload = [
            'searchKey'   => $this->selectedOption,
            'searchValue' => $this->inputValue,
        ];
        if ($results->isNotEmpty()) {
            $payload['results'] = $results->toArray();
            $payload['count']   = $results->count();
        }
        // dd($payload);
        $this->dispatch('beneficiary-search', data: $payload);
    }
    public function render()
    {
        return view('livewire.beneficiary-search');
    }
}
