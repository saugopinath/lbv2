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
        if ($this->isSearchPerformed) {
            $query = BeneficiaryPersonalDetail::query()->whereIn('application_id', $this->applicationIds);
        } else {
            $query = BeneficiaryPersonalDetail::query();
            if (!empty($this->schemes)) {
                $query->whereIn(
                    'scheme_id',

                    $this->schemes
                );
            }
            if (!empty($this->filter_condition)) {
                foreach ($this->filter_condition as $key => $value) {
                    $query->where($key, $value);
                }
            }
        }
        $beneficiaries = $query
            ->with(['enclosers', 'scheme', 'contact.district'])
            ->paginate(20)
            ->through(function ($b) {
                $encryptedId = Crypt::encryptString($b->application_id);
                $b->encryptedId = $encryptedId;
                $b->paymentUrl = route('beneficiary-payment-history-log');
                $b->BenDetailsUrl = route('beneficiary-details');

                // Status Logic
                $status = NULL;
                $statusColor = 'gray';
                if ($b->is_final == 0 && $b->next_level_role_id == NULL) {
                    $status = 'Application Partial Entry';
                    $statusColor = 'yellow';
                } elseif ($b->is_final == 1 && $b->next_level_role_id == 0) {
                    $status = 'Application Final Submitted';
                    $statusColor = 'orange';
                } elseif ($b->is_final == 1 && $b->next_level_role_id == WorkflowsteproleMapping::where('scheme_id', $b->scheme_id)->where('module_id', Null)->where('rank', 2)->value('next_label_role_id')) {
                    $status = 'Verified';
                    $statusColor = 'blue';
                } elseif ($b->is_final == 1 && $b->next_level_role_id == WorkflowsteproleMapping::where('scheme_id', $b->scheme_id)->where('module_id', Null)->where('rank', 3)->value('next_label_role_id')) {
                    $status = 'Approved';
                    $statusColor = 'green';
                } else {
                    $status = 'Rejected';
                    $statusColor = 'red';
                }
                $b->status = $status;
                $b->statusColor = $statusColor;

                // Mobile Masking
                $mobile = $b->other_details['mobile_no'] ?? null;
                $b->maskedMobile = $mobile ? substr($mobile, 0, 2) . 'XXXXXX' . substr($mobile, -2) : 'N/A';

                // Relation Logic
                $relation = 'N/A';
                $relationName = 'N/A';
                if (!empty($b->ben_father_name)) {
                    $relation = 'Father';
                    $relationName = $b->ben_father_name;
                } elseif (!empty($b->ben_mother_name)) {
                    $relation = 'Mother';
                    $relationName = $b->ben_mother_name;
                } elseif (!empty($b->ben_spouse_name)) {
                    $relation = 'Spouse';
                    $relationName = $b->ben_spouse_name;
                }
                $b->relation = $relation;
                $b->relationName = $relationName;

                $b->location = ($b->contact->district->name ?? 'Unknown') . ', West Bengal';

                // Profile Picture Logic
                $b->ben_profile_pic = $b->enclosers()
                    ->where('document_type', 103)
                    ->first()?->toArray() ?? [];

                return $b;
            });
        $isSingle = $beneficiaries->count() === 1;

        return view(
            'livewire.track-beneficiary.track-beneficiary-data',
            [
                'beneficiaries' => $beneficiaries,
                'isSingle' => $isSingle,
            ]
        );
    }
}
