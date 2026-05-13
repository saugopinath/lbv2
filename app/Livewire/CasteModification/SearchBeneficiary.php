<?php

namespace App\Livewire\CasteModification;

use App\Helpers\FormOptionHelper;
use App\Models\BeneficiaryAadhaar;
use App\Models\BeneficiaryCommonList;
use App\Models\BeneficiaryPersonalDetail;
use App\Models\CasteModificationInfo;
use App\Models\Codemaster;
use App\Models\Scheme;
use App\Models\SchemeTabFormField;
use App\Models\WorkflowsteproleMapping;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;
use App\Services\WorkflowService;

class SearchBeneficiary extends Component
{
    public $searchType = '';
    public $selectScheme = '';
    public $searchValue = '';
    public $searchBy = '';
    public $results = null;
    public $items = [];
    public $currentLabel = 'Select Search Type';
    public $filter_condition = [];
    public $verified_code = null;
    public $aproved_code = null;
    public $revert_code = null;
    public $schemeOptions = [];
    public $getMinMaxWorkflowStep;
    public $nextLevelRoleId;
    public $filterRoleId;
    public $CasteOptions = [];

    public $searchOptions = [
        1 => 'Application ID',
        2 => 'Beneficiary ID',
        3 => 'Aadhaar Number',
        4 => 'Mobile No',
    ];
    protected $searchTypeMap = [
        1 => 'application_id',
        2 => 'beneficiary_id',
        3 => 'aadhaar_vault',
        4 => 'mobile_no',
    ];

    public function mount(): void
    {
        $select_lgd = session('lgd_session');
        if (!empty($select_lgd['district_id'])) {
            $this->filter_condition['created_by_dist_code'] = Crypt::decryptString($select_lgd['district_id']);
        }
        if (!empty($select_lgd['block_id'])) {
            $this->filter_condition['created_by_local_body_code'] = Crypt::decryptString($select_lgd['block_id']);
        }
        if (!empty($select_lgd['subdivision_id'])) {
            $this->filter_condition['created_by_local_body_code'] = Crypt::decryptString($select_lgd['subdivision_id']);
        }
        // $formOptions  = json_decode(file_get_contents(public_path('js/form-options.json')), true);
        // $this->CasteOptions = $formOptions['Caste'] ?? [];
        // dd($this->CasteOptions);
        $this->CasteOptions = FormOptionHelper::get('Caste');
        $this->schemeOptions = Scheme::where('is_active', true)->pluck('name', 'id')->toArray();
        // dd($this->schemeOptions);
        $this->verified_code = Codemaster::getIdByCode(2202);
        $this->aproved_code = Codemaster::getIdByCode(2203);
        $this->revert_code = Codemaster::getIdByCode(2204);
    }

    // public function updatedSearchType($value)
    // {
    //     if (empty($value) || !isset($this->searchOptions[$value])) {
    //         $this->currentLabel = 'Select Search Applicant By First';
    //         $this->searchValue = '';
    //         return;
    //     }
    //     $this->currentLabel = $this->searchOptions[$value];
    //     $this->reset('searchValue');
    // }

    public function updatedSearchType($value)
    {
        if (empty($value) || !isset($this->searchOptions[$value])) {
            $this->currentLabel = 'Select Search Applicant By First';
            $this->reset([
                'searchType',
                'searchValue',
                'results',
                'items',
                'getMinMaxWorkflowStep',
                'nextLevelRoleId',
                'filterRoleId',
            ]);
            $this->resetValidation();
            return;
        }
        $this->currentLabel = $this->searchOptions[$value];
        $this->reset([
            'searchValue',
            'results',
            'items',
            'getMinMaxWorkflowStep',
            'nextLevelRoleId',
            'filterRoleId',
        ]);
        $this->resetValidation();
    }



    protected function rules()
    {
        return [
            'selectScheme' => 'required',
            'searchType'  => 'required|in:1,2,3,4',
            'searchValue' => ['required', function ($attribute, $value, $fail) {
                switch ($this->searchType) {
                    case 1: // Application ID
                    case 2: // Beneficiary ID
                        if (!is_numeric($value)) {
                            $fail('This field must be numeric.');
                        }
                        break;
                    case 3: // Aadhaar Number
                        if (!preg_match('/^\d{12}$/', $value)) {
                            $fail('Aadhaar number must be exactly 12 digits.');
                        }
                        break;
                    case 4: // Mobile No
                        if (!preg_match('/^\d{10}$/', $value)) {
                            $fail('Mobile number must be exactly 10 digits.');
                        }
                        break;
                    default:
                        $fail('Invalid search type selected.');
                }
            }]
        ];
    }

    protected $messages = [
        'selectScheme.required' => 'Please select a scheme.',
        'searchType.required' => 'Please select a search type.',
        'searchType.in'       => 'Invalid search type selected.',
        'searchValue.required' => 'Please enter a value to search.',
    ];

    public function search(WorkflowService $workflowService)
    {
        // dd($this->selectScheme);
        $this->validate();
        $column = $this->searchTypeMap[$this->searchType];
        $modelClass   = null;
        $searchValue  = $this->searchValue;
        $query        = null;
        if (in_array($column, ['application_id', 'beneficiary_id'])) {
            $modelClass   = BeneficiaryPersonalDetail::class;
            $searchColumn = $column;
        } elseif ($column === 'aadhaar_vault') {
            $modelClass   = BeneficiaryAadhaar::class;
            $searchColumn = 'aadhaar_vault';
            $searchValue  = md5($this->searchValue);
        } else {
            $fieldManager = SchemeTabFormField::with('tabMaster')
                ->where('field_name', $column)
                ->where('scheme_id', $this->selectScheme)
                ->first();
            if (!$fieldManager || !$fieldManager->tabMaster) {
                $message = "No Beneficiary found in this Scheme.";
                session()->flash('xwarning', $message);
                return;
            }
            $modelClass   = "App\\Models\\" . $fieldManager->tabMaster->tab_model_name;
            $dbColumn     = $fieldManager->db_column;
            $fieldName    = $fieldManager->field_name;
            // dd($dbColumn, $fieldName);
            if ($dbColumn == 'other_details') {
                $searchColumn = 'other_details';
            } else {
                $searchColumn = $dbColumn;
            }
        }
        $query = $modelClass::query()
            ->select('application_id')
            ->where('scheme_id', $this->selectScheme);

        if ($searchColumn == 'other_details') {
            // dd('query');
            $query->whereRaw(
                "other_details ->> ? = ?",
                [$fieldName, $searchValue]
            );
        } else {
            $query->where($searchColumn, $searchValue);
        }
        // $sql = vsprintf(
        //     str_replace('?', "'%s'", $query->toSql()),
        //     $query->getBindings()
        // );
        // dd($sql);
        $applicationId = $query->value('application_id');
        // dd($applicationId);
        if (!$applicationId) {
            // dd('fdfd');
            $message = "No matching beneficiary found.";
            session()->flash('xwarning', $message);
            $this->items = [];
            // $this->dispatch(
            //     'toaster',
            //     type: 'warning',
            //     title: 'Not Found',
            //     message: 'No matching beneficiary found.'
            // );
            return;
        } else {
            $existingRecord = null;
            $existingRecord = CasteModificationInfo::where('application_id',  $applicationId)->where('scheme_id', $this->selectScheme)->where('is_active', true);
            // if (!empty($this->filter_condition)) {
            //     // dd($this->filter_condition);
            //     $existingRecord->where($this->filter_condition);
            // }
            $existingRecord = $existingRecord->first();
            if ($existingRecord) {
                // dd($existingRecord);
                if ($existingRecord->next_level_requested_id == $this->verified_code) {
                    $message = "Request already Verified by the Verifier.";
                } elseif ($existingRecord->next_level_requested_id == $this->aproved_code) {
                    $message = "Request already Approved By the Approver.";
                } elseif ($existingRecord->next_level_requested_id == $this->revert_code) {
                    $message = "Request is reverted.";
                } else {
                    $message = "Caste modification already requested.";
                }
                // dd($message);
                session()->flash('xwarning', $message);
                $this->items = [];
                return;
            } else {
                $this->getMinMaxWorkflowStep = WorkflowsteproleMapping::getMinMaxWorkflowStep($this->selectScheme);
                $this->nextLevelRoleId = $workflowService->getLevelRoles($this->selectScheme, $this->getMinMaxWorkflowStep['max']);
                $this->filterRoleId = $this->nextLevelRoleId->next_level_role_id;
                // dd($this->filterRoleId);
                // dd($this->getMinMaxWorkflowStep);
                // $query = BeneficiaryPersonalDetail::select('application_id', 'beneficiary_id', 'scheme_id', 'beneficiary_name', 'caste', 'caste_cer_no', 'other_details', 'next_level_role_id')->where('application_id', $applicationId)->where('scheme_id', $this->selectScheme)->where('is_clean', 1)->where('is_final', 1);
                // // dd($query->toSql(), $query->getBindings());
                // // $this->getMinMaxWorkflowStep = WorkflowsteproleMapping::getMinMaxWorkflowStep($this->selectScheme);
                // // dd($query->toSql(), $query->getBindings());
                // if (!empty($this->filter_condition)) {
                //     $query->where($this->filter_condition);
                // }
                // $this->results = $query->get();
                $query = BeneficiaryPersonalDetail::query()
                    ->select([
                        'application_id',
                        'beneficiary_id',
                        'scheme_id',
                        'beneficiary_name',
                        'caste',
                        'caste_cer_no',
                        'other_details',
                        'next_level_role_id',
                    ])
                    ->where('application_id', $applicationId)
                    ->where('scheme_id', $this->selectScheme)
                    ->where('is_clean', 1)
                    ->where('is_final', 1);
                if (!empty($this->filter_condition)) {
                    foreach ($this->filter_condition as $key => $value) {
                        $query->where($key, $value);
                    }
                }
                $this->results = $query->get();
                // dd($this->results);
                if ($this->results->isEmpty()) {
                    $this->items = [];
                    $message = "No matching beneficiary found.";
                    session()->flash('xerror', $message);
                    return;
                }
                // dd($this->results->filter(function ($item) {
                //     return $item->next_level_role_id == $this->filterRoleId;
                // }));
                // if ($this->results->next_level_role_id != $this->filterRoleId) {
                //     session()->flash('xwarning', 'These Beneficiary is not approved Yet.');
                //     $this->items = [];
                //     return;
                // }
                $approvedItems = $this->results->filter(function ($item) {
                    return $item->next_level_role_id == $this->filterRoleId;
                });
                if ($approvedItems->isEmpty()) {
                    $message = "These Beneficiary is not approved Yet.";
                    session()->flash('xwarning', $message);
                    $this->items = [];
                    return;
                } else {
                    // dd($approvedItems->toArray());
                    $this->items = $approvedItems->map(function ($item) {
                        return [
                            'application_id' => $item->application_id ?? '-',
                            'beneficiary_id' => $item->beneficiary_id ?? '-',
                            'mobile_no'      => $item->other_details['mobile_no'] ?? '-',
                            'applicant_name' => $item->beneficiary_name ?? '-',
                            'Caste_name' => FormOptionHelper::label('Caste', $item->caste) ?? 'Unknown',
                            'scheme_id' => $item->scheme_id ?? '-',
                        ];
                    })->values();
                }
            }
        }
    }
    public function render()
    {
        return view('livewire.caste-modification.search-beneficiary', [
            'searchOptions' => $this->searchOptions,
        ]);
    }
}
