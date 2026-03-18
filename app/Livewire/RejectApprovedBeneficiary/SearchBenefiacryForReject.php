<?php

namespace App\Livewire\RejectApprovedBeneficiary;

use App\Models\BeneficiaryCommonList;
use App\Models\CasteModificationInfo;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;

class SearchBenefiacryForReject extends Component
{
    public $searchType = '';
    public $searchValue = '';
    public $searchBy = '';
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
        3 => 'encoded_aadhar',
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
        // dd('ok');
        $this->validate();
        $column = $this->searchTypeMap[$this->searchType];
        if ($column == 'encoded_aadhar') {
            $this->searchBy = md5($this->searchValue);
        } else {
            $this->searchBy = $this->searchValue;
        }

        // if($column == 'encoded_aadhar'){
        //     // dump('encoded_aadhar');
        // $this->searchValue =md5($this->searchValue);
        // // dump($this->searchValue);
        // }
        $query = BeneficiaryCommonList::query()->with('sourceable');
        $query->where('sourceable_type', 'App\Models\BeneficiaryPersonal')->where($column, $this->searchBy);
        // dd($query->toSql(), $query->getBindings());
        if (!empty($this->filter_condition)) {
            $query->where($this->filter_condition);
        }

        $this->results = $query->get();

        if ($this->results->isEmpty()) {
            $this->items = [];
            $message = "No matching beneficiary found Or Beneficiary is not approved Yet.";
            session()->flash('xerror', $message);
            return;
        }

        $this->items = $this->results->map(function ($item) {
            return [
                'application_id' => $item->sourceable->application_id ?? '-',
                'beneficiary_id' => $item->sourceable->beneficiary_id ?? '-',
                'mobile_no'      => $item->sourceable->mobile_no ?? '-',
                'applicant_name' => $item->sourceable->full_name ?? '-',
                'district'       => $item->sourceable->contact->district->name ?? '-',
                'block'          => $item->sourceable->contact->block->name ?? '-',
                'panchayat'      => $item->sourceable->contact->panchayat->name ?? '-',
                'bank_account'   => $item->sourceable->bank->bank_account_number ?? '-',
                'ifsc'           => $item->sourceable->bank->ifsc ?? '-',
            ];
        })->values();
    }
    public function render()
    {
        return view('livewire.reject-approved-beneficiary.search-benefiacry-for-reject');
    }
}
