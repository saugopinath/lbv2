<?php

namespace App\Livewire\TrackBeneficiary;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

use App\Models\BeneficiaryPersonalDetail;
use App\Models\WorkflowsteproleMapping;
use App\Models\District;
use App\Models\Scheme;
use App\Models\BeneficiaryEnclosure;
use Illuminate\Support\Facades\Crypt;

class TrackBeneficiaryData extends Component
{
    use WithPagination;

    public $search = '';
    public $searchKey = '';
    public $applicationIds = [];
    public $isSearchPerformed = false;
    public $scheme = '';
    public $district = '';
    public $urban_code = '';
    public $block = '';
    public $gp_ward = '';

    public $schemes = [];
    public $filter_condition = [];

    public function updating()
    {
        $this->resetPage();
    }

    protected $listeners = [
        'beneficiary-search' => 'handleSearch',
        'reset-beneficiary-search' => 'resetSearch',
    ];

    public function handleSearch($data)
    {
        // dd($data);
        $this->search = $data['searchValue'] ?? '';
        $this->searchKey = $data['searchKey'] ?? '';
        $this->applicationIds = collect($data['results'] ?? [])->pluck('application_id')->toArray();
        $this->isSearchPerformed = true;
        $this->resetPage();
    }

    public function resetSearch()
    {
        $this->search = '';
        $this->searchKey = '';
        $this->applicationIds = [];
        $this->isSearchPerformed = false;
        $this->resetPage();
    }

    public function mount()
    {
        $select_lgd = session('lgd_session');
        // dd($select_lgd);
        if (!empty($select_lgd['district_id'])) {
            $this->filter_condition['created_by_dist_code']
                = Crypt::decryptString(
                    $select_lgd['district_id']
                );
        }
        if (!empty($select_lgd['block_id'])) {
            $this->filter_condition['created_by_local_body_code']
                = Crypt::decryptString(
                    $select_lgd['block_id']
                );
        }
        if (!empty($select_lgd['subdivision_id'])) {
            $this->filter_condition['created_by_local_body_code']
                = Crypt::decryptString(
                    $select_lgd['subdivision_id']
                );
        }
        if (!empty($select_lgd['scheme_id'])) {
            $schemeRaw = is_array($select_lgd['scheme_id'])
                ? $select_lgd['scheme_id']
                : [$select_lgd['scheme_id']];

            $this->schemes = array_map(
                fn($id) => Crypt::decryptString($id),
                $schemeRaw
            );
        }
    }

    public function render()
    {
        $query = BeneficiaryPersonalDetail::query();
        if ($this->isSearchPerformed) {
            $query->whereIn('application_id', $this->applicationIds);
        } else {
            if (!empty($this->schemes)) {
                $query->whereIn('scheme_id', $this->schemes);
            }
            if (!empty($this->filter_condition)) {
                foreach ($this->filter_condition as $key => $value) {
                    $query->where($key, $value);
                }
            }
        }
        $beneficiaries = $query
            ->with([
                'scheme',
                'contact.district',
                'enclosers'
            ])
            ->paginate(20);
        $schemeIds = $beneficiaries->pluck('scheme_id')->unique();
        $workflowMappings = WorkflowsteproleMapping::whereIn('scheme_id', $schemeIds)
            ->whereNull('module_id')
            ->whereIn('rank', [2, 3])
            ->get()
            ->groupBy('scheme_id');
        $beneficiaries->getCollection()->transform(function ($b) use ($workflowMappings) {
            $encryptedId = Crypt::encryptString($b->application_id);
            $b->encryptedId = $encryptedId;
            $b->paymentUrl = route('beneficiary-payment-history-log');
            $b->BenDetailsUrl = route('beneficiary-details');
            $verifiedRole = optional(
                $workflowMappings[$b->scheme_id]
                    ->firstWhere('rank', 2)
            )->next_label_role_id;

            $approvedRole = optional(
                $workflowMappings[$b->scheme_id]
                    ->firstWhere('rank', 3)
            )->next_label_role_id;

            match (true) {
                $b->is_final == 0 && is_null($b->next_level_role_id) => [
                    $b->status,
                    $b->statusColor
                ] = ['Application Partial Entry', 'yellow'],

                $b->is_final == 1 && $b->next_level_role_id == 0 => [
                    $b->status,
                    $b->statusColor
                ] = ['Application Final Submitted', 'orange'],

                $b->next_level_role_id == $verifiedRole => [
                    $b->status,
                    $b->statusColor
                ] = ['Verified', 'blue'],

                $b->next_level_role_id == $approvedRole => [
                    $b->status,
                    $b->statusColor
                ] = ['Approved', 'green'],

                default => [
                    $b->status,
                    $b->statusColor
                ] = ['Rejected', 'red'],
            };
            $mobile = data_get($b, 'other_details.mobile_no');
            $b->maskedMobile = $mobile
                ? substr($mobile, 0, 2) . 'XXXXXX' . substr($mobile, -2)
                : 'N/A';

            $relations = [
                'Father' => $b->ben_father_name,
                'Mother' => $b->ben_mother_name,
                'Spouse' => $b->ben_spouse_name,
            ];

            $b->relation = 'N/A';
            $b->relationName = 'N/A';
            foreach ($relations as $type => $name) {
                if (!empty($name)) {
                    $b->relation = $type;
                    $b->relationName = $name;
                    break;
                }
            }
            $district = optional($b->contact?->district)->name;

            $b->location = $district
                ? $district . ', West Bengal'
                : 'Unknown';
            $b->ben_profile_pic = $b->enclosers
                ->where('document_type', 103)
                ->first();

            return $b;
        });

        return view(
            'livewire.track-beneficiary.track-beneficiary-data',
            [
                'beneficiaries' => $beneficiaries,
                'isSingle' => $beneficiaries->count() === 1,
            ]
        );
    }
}
