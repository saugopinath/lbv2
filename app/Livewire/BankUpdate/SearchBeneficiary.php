<?php

namespace App\Livewire\BankUpdate;

use Livewire\Component;
use App\Models\BeneficiaryPersonalDetail;
use App\Models\Scheme;
use Illuminate\Support\Facades\Crypt;
use App\Services\WorkflowService;
use App\Models\WorkflowsteproleMapping;
class SearchBeneficiary extends Component
{
    public $searchType = '';
    public $searchValue = '';
    public $searchBy = '';
    public $results = null;
    public $schemeOptions = [];
    public $items = [];
    public $currentLabel = 'Select Search Type';
    public $filter_condition = [];

    public $selectScheme = '';

    public $searchOptions = [
        1 => 'Application ID',
        2 => 'Beneficiary ID',
        3 => 'Aadhaar Number',
        4 => 'Mobile No',
    ];

    protected $searchTypeMap = [
        1 => 'application_id',
        2 => 'beneficiary_id',
        3 => 'encoded_aadhaar',
        4 => 'mobile_no',
    ];

    public function mount(): void
    {
        $select_lgd = session('lgd_session');

        if (!empty($select_lgd['district_id'])) {
            $this->filter_condition['district_id'] = Crypt::decryptString($select_lgd['district_id']);
        }
        if (!empty($select_lgd['block_id'])) {
            $this->filter_condition['block_id'] = Crypt::decryptString($select_lgd['block_id']);
        }
        if (!empty($select_lgd['subdivision_id'])) {
            $this->filter_condition['sub_division_id'] = Crypt::decryptString($select_lgd['subdivision_id']);
        }

        $this->schemeOptions = Scheme::where('is_active', true)
            ->pluck('name', 'id')
            ->toArray();
    }

    public function updatedSearchType($value)
    {
        if (empty($value) || !isset($this->searchOptions[$value])) {
            $this->currentLabel = 'Select Search Applicant By First';
            $this->searchValue = '';
            return;
        }

        $this->currentLabel = $this->searchOptions[$value];
        $this->reset('searchValue');
    }

    protected function rules()
    {
        return [
            'selectScheme' => 'required',
            'searchType' => 'required|in:1,2,3,4',
            'searchValue' => [
                'required',
                function ($attribute, $value, $fail) {
                    switch ($this->searchType) {
                        case 1:
                        case 2:
                            if (!is_numeric($value)) {
                                $fail('This field must be numeric.');
                            }
                            break;
                        case 3:
                            if (!preg_match('/^\d{12}$/', $value)) {
                                $fail('Aadhaar must be 12 digits.');
                            }
                            break;
                        case 4:
                            if (!preg_match('/^\d{10}$/', $value)) {
                                $fail('Mobile must be 10 digits.');
                            }
                            break;
                    }
                }
            ]
        ];
    }

    public function search(WorkflowService $workflowService)
    {
        $this->validate();

        // 🔹 Workflow setup
        $this->getMinMaxWorkflowStep = WorkflowsteproleMapping::getMinMaxWorkflowStep($this->selectScheme);

        $this->nextLevelRoleId = $workflowService->getLevelRoles(
            $this->selectScheme,
            $this->getMinMaxWorkflowStep['max']
        );

        $this->filterRoleId = $this->nextLevelRoleId->next_level_role_id;

        // 🔹 Search column
        $column = $this->searchTypeMap[$this->searchType] ?? null;

        if (!$column) {
            session()->flash('xerror', 'Invalid search type.');
            return;
        }
        
        $this->searchBy = ($column === 'encoded_aadhaar')
            ? md5(trim($this->searchValue))
            : trim($this->searchValue);
       
        $query = BeneficiaryPersonalDetail::query()
            ->with(['contact', 'banks', 'aadhaar', 'scheme']);
       
        if (!empty($this->selectScheme)) {
            $query->where('scheme_id', $this->selectScheme);
        }
      
        $query->where('next_level_role_id', $this->filterRoleId);
        
        if ($column === 'encoded_aadhaar') {

            $query->whereHas('aadhaar', function ($q) {
                $q->where('encoded_aadhaar', $this->searchBy);
            });

        } elseif ($column === 'mobile_no') {
           
            $query->where('other_details->mobile_no', $this->searchBy);

        } else {

            $query->where($column, $this->searchBy);
        }
      
        if (!empty($this->filter_condition)) {
            $query->whereHas('contact', function ($q) {
                foreach ($this->filter_condition as $key => $value) {
                    $q->where($key, $value);
                }
            });
        }
     
        $this->results = $query->get();

        if ($this->results->isEmpty()) {
            $this->items = [];
            session()->flash('xerror', 'No data found.');
            return;
        }
      
        $this->items = $this->results->map(fn($item) => [
            'application_id' => $item->application_id ?? '-',
            'beneficiary_id' => $item->beneficiary_id ?? '-',
            
            'mobile_no' => $item->other_details['mobile_no'] ?? '-',

            'applicant_name' => $item->beneficiary_name ?? '-',
            'scheme_name' => $item->scheme->name ?? '-',

            'district' => $item->contact->district->name ?? '-',
            'block' => $item->contact->block->name ?? '-',
            'panchayat' => $item->contact->panchayat->name ?? '-',

            'bank_account' => $item->banks->bankaccountnumber ?? '-',
            'ifsc' => $item->banks->ifscode ?? '-',
        ])->values();
    }

    public function render()
    {
        return view('livewire.bank-update.search-beneficiary');
    }
}