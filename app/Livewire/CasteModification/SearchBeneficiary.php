<?php

namespace App\Livewire\CasteModification;

use App\Models\BeneficiaryCommonList;
use App\Models\CasteModificationInfo;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;

class SearchBeneficiary extends Component
{
    public $searchType = '';
    public $searchValue = '';
    public $results = null;
    public $items = [];
    public $currentLabel = 'Select Search Type';
    public $filter_condition = [];

    public $searchOptions = [
        1 => 'Application ID',
        2 => 'Beneficiary ID',
        3 => 'Aadhar Number',
        4 => 'Mobile No',
    ];

    protected $searchTypeMap = [
        1 => 'sourceable_id',
        2 => 'beneficiary_id',
        3 => 'aadhar_number',
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
            $this->filter_condition['subdivision_id'] = Crypt::decryptString($select_lgd['subdivision_id']);
        }
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
            'searchType'  => 'required|in:1,2,3,4',
            'searchValue' => ['required', function ($attribute, $value, $fail) {
                switch ($this->searchType) {
                    case 1: // Application ID
                    case 2: // Beneficiary ID
                        if (!is_numeric($value)) {
                            $fail('This field must be numeric.');
                        }
                        break;
                    case 3: // Aadhar Number
                        if (!preg_match('/^\d{12}$/', $value)) {
                            $fail('Aadhar number must be exactly 12 digits.');
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
        'searchType.required' => 'Please select a search type.',
        'searchType.in'       => 'Invalid search type selected.',
        'searchValue.required' => 'Please enter a value to search.',
    ];

    public function search()
    {
        $this->validate();
        $column = $this->searchTypeMap[$this->searchType];
        $existingRecord = CasteModificationInfo::where('application_id', $this->searchValue)->first();
        if ($existingRecord) {
            if ($existingRecord->next_level_requested_id == 148) {
                $message = "Request already Verified by the Verifier.";
            } elseif ($existingRecord->next_level_requested_id == 149) {
                $message = "Request already Approved By the Approver.";
            } elseif ($existingRecord->next_level_requested_id == 150) {
                $message = "Request is reverted.";
            } else {
                $message = "Caste modification already requested.";
            }
            session()->flash('warning', $message);
            $this->items = [];
            return;
        } else {
            $query = BeneficiaryCommonList::query()->with('sourceable');
            $query->where($column, $this->searchValue);

            if (!empty($this->filter_condition)) {
                $query->where($this->filter_condition);
            }

            $this->results = $query->get();

            if ($this->results->isEmpty()) {
                $this->items = [];
                $message = "No matching beneficiary found.";
                session()->flash('warning', $message);
                return;
            }

            $approvedItems = $this->results->filter(function ($item) {
                return $item->sourceable_type == 'App\Models\BeneficiaryPersonal';
            });

            if ($approvedItems->isEmpty()) {
                $message = "These Beneficiaries are not approved Yet.";
                session()->flash('warning', $message);
                $this->items = [];
                return;
            } else {
                $this->items = $approvedItems->map(function ($item) {
                    return [
                        'application_id' => $item->sourceable->application_id ?? '-',
                        'beneficiary_id' => $item->sourceable->beneficiary_id ?? '-',
                        'mobile_no'      => $item->sourceable->mobile_no ?? '-',
                        'applicant_name' => $item->sourceable->full_name ?? '-',
                        'Caste_name'     => $item->sourceable->castes->name ?? '-',
                    ];
                })->values();
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
