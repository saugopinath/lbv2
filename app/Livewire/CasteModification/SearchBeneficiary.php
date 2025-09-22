<?php

namespace App\Livewire\CasteModification;

use App\Models\BeneficiaryCommonList;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;

class SearchBeneficiary extends Component
{
    public $searchType = ''; // default
    public $searchValue = '';
    public $results = null;
    public $currentLabel = 'Select Search Type';
    public $filter_condition = [];
    public $items = [];

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

        //    dd($this->filter_condition);
    }

    public function updatedSearchType($value)
    {
        if (empty($value) || !isset($this->searchOptions[$value])) {
            $this->currentLabel = 'Select Search Applicant By First';
            $this->searchValue = '';
            return;
        }

        $this->currentLabel = $this->searchOptions[$value];
        // only reset when user first chooses a type
        $this->reset('searchValue');
    }
    protected function rules()
    {
        return [
            'searchType'  => 'required|in:1,2,3,4',

        ];
    }

    protected $messages = [
        'searchType.required'  => 'Please select a search type.',
        'searchValue.required' => 'Please enter a value to search.',
        'searchValue.numeric'  => 'This field must be a number.',
        'searchValue.digits'   => 'Aadhar must be exactly 12 digits.',
    ];
    // public function updatedSearchValue($value)
    // {
    //     dd($value);
    //     $this->searchValue = $value;
    //     dd($this->searchValue);
    // }
    public function search()
    {
        $this->validate();
        $user = Auth::user();
        $column = $this->searchTypeMap[$this->searchType];
        $query = BeneficiaryCommonList::query()->with('sourceable');

        $query->where($column, $this->searchValue);

        if (!empty($this->filter_condition)) {
            $query->where($this->filter_condition);
        }

        $this->results = $query->get();
        // dd($this->results);
        $this->items = $this->results->map(function ($item) {
            return [
                'application_id' => $item->sourceable->application_id,
                'beneficiary_id' => $item->sourceable->beneficiary_id,
                // 'aadhar_number' => $item->sourceable->aadhar->bank_account_number,
                'mobile_no' => $item->sourceable->mobile_no,
                'applicant_name' => $item->sourceable->full_name,
                'Caste_name' => $item->sourceable->castes->name,
            ];
        });
        // dd($this->items);
    }

    public function render()
    {
        return view('livewire.caste-modification.search-beneficiary', [
            'searchOptions' => $this->searchOptions,
        ]);
    }
}
