<?php

namespace App\Livewire\RejectApprovedBeneficiary;

use App\Helpers\FormOptionHelper;
use App\Models\BeneficiaryAadhaar;
use App\Models\BeneficiaryCommonList;
use App\Models\BeneficiaryPersonalDetail;
use App\Models\CasteModificationInfo;
use App\Models\Scheme;
use App\Models\SchemeTabFormField;
use App\Models\WorkflowsteproleMapping;
use App\Services\WorkflowService;
use Illuminate\Support\Facades\Crypt;
use Livewire\Component;

class SearchBenefiacryForReject extends Component
{
    public $items = [];
    public $filter_condition = [];

    protected $listeners = [
        'beneficiary-search' => 'handleSearch'
    ];
    public function mount(): void
    {
        // dd('mount');
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
    }
    public function handleSearch($data)
    {
        if (empty($data['results'])) {
            $this->items = [];
            session()->flash('xwarning', 'No matching approved beneficiary found.');
            return;
        }
        $applicationIds = collect($data['results'])->pluck('application_id')->toArray();
        // $this->items = BeneficiaryPersonalDetail::query()
        //     ->with([
        //         'contact:scheme_id,application_id,beneficiary_id,district_id,rural_urban,blockurban,gpward',
        //         'bank:scheme_id,application_id,beneficiary_id,bankaccountnumber,ifscode'
        //     ])
        //     ->whereIn('application_id', $applicationIds)
        //     ->when(!empty($this->filter_condition), function ($query) {
        //         foreach ($this->filter_condition as $key => $value) {
        //             $query->where($key, $value);
        //         }
        //     })
        //     ->get()
        //     ->map(function ($item) {
        //         return [
        //             'application_id' => $item->application_id ?? '-',
        //             'beneficiary_id' => $item->beneficiary_id ?? '-',
        //             'mobile_no'      => $item->other_details['mobile_no'] ?? '-',
        //             'applicant_name' => $item->beneficiary_name ?? '-',
        //             'address'        => optional($item->contact)->getFullAddress() ?? 'N/A',
        //             'bank_account'   => optional($item->bank)->bankaccountnumber ?? '-',
        //             'ifsc'           => optional($item->bank)->ifscode ?? '-',
        //             'scheme_id'      => $item->scheme_id ?? '-',
        //         ];
        //     })->values()->toArray();
        $this->items = BeneficiaryPersonalDetail::query()
            ->select([
                'application_id',
                'beneficiary_id',
                'scheme_id',
                'beneficiary_name',
                'other_details',
            ])
            ->with([
                'contact:beneficiary_id,application_id,scheme_id,district_id,rural_urban,blockurban,gpward',
                'bank:beneficiary_id,application_id,scheme_id,bankaccountnumber,ifscode'
            ])
            ->whereIn('application_id', $applicationIds)
            ->where([
                ['is_clean', '=', 1],
                ['is_final', '=', 1],
            ])
            ->when(!empty($this->filter_condition), function ($query) {
                foreach ($this->filter_condition as $key => $value) {
                    $query->where($key, $value);
                }
            })
            ->get()
            ->map(fn($item) => [
                'application_id' => $item->application_id ?? '-',
                'beneficiary_id' => $item->beneficiary_id ?? '-',
                'mobile_no'      => $item->other_details['mobile_no'] ?? '-',
                'applicant_name' => $item->beneficiary_name ?? '-',
                'address'        => optional($item->contact)->getFullAddress() ?? 'N/A',
                'bank_account'   => optional($item->bank)->bankaccountnumber ?? '-',
                'ifsc'           => optional($item->bank)->ifscode ?? '-',
                'scheme_id'      => $item->scheme_id ?? '-',
            ])
            ->values()
            ->toArray();

        if (empty($this->items)) {
            session()->flash('xwarning', 'Beneficiary found but your access level does not allow viewing them.');
        }
    }

    public function render()
    {
        return view('livewire.reject-approved-beneficiary.search-benefiacry-for-reject');
    }
}
