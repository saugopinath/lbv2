<?php

namespace App\Livewire;

use App\Models\ApplicantIncompletDeatil;
use App\Models\District;
use Livewire\Component;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\IncompleteExport;
use App\Models\Block;
use App\Models\Municipality;
use App\Models\Panchayat;
use App\Models\Subdivision;

class IncompleteMisReportTable extends Component
{
    public $incomplete_type = null;
    public ?string $filterCode = null;
    public $district_id, $rural_urban, $blockurban, $gp_ward, $selectedSubdivision;

    protected $listeners = [
        'doSearch' => 'updateFilters',
    ];

    public $loginDistrictCode, $loginSubdivisionCode, $loginBlockCode;
    public array $filter_condition = [];

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
    public function updateFilters($filters)
    {
        // dd($filters);
        $this->district_id = $filters['district_id'] ?? null;
        $this->rural_urban = $filters['rural_urban'] ?? null;
        $this->selectedSubdivision = $filters['subdivision_id'] ?? null;
        $this->blockurban = $filters['blockurban'] ?? null;
        $this->gp_ward = $filters['gp_ward'] ?? null;
        $this->filterCode = $filters['incomplete_type'] ?? null;
        // dd($this->filterCode);
    }

    public function getVerifierPendingProperty()
    {
        return ApplicantIncompletDeatil::query()
            ->whereNull('next_level_request_id')
            ->when(
                isset($this->filterCode),
                fn($q) =>
                $q->where('incomplet_type',  $this->filterCode)
            )
            ->whereHas('beneficiaryCommonList', function ($q) {
                foreach ($this->filter_condition as $col => $val) {
                    if ($col !== 'incomplete_type') {
                        $q->where($col, $val);
                    }
                }
            })
            ->select('application_id')
            ->groupBy('application_id')
            ->get()
            ->count();
    }

    public function getVerifierProperty()
    {
        return ApplicantIncompletDeatil::query()
            ->where('next_level_request_id', 1)
            ->when(
                isset($this->filterCode),
                fn($q) =>
                $q->where('incomplet_type',  $this->filterCode)
            )
            ->whereHas('beneficiaryCommonList', function ($q) {
                foreach ($this->filter_condition as $col => $val) {
                    if ($col !== 'incomplete_type') {
                        $q->where($col, $val);
                    }
                }
            })
            ->select('application_id')
            ->groupBy('application_id')
            ->get()
            ->count();
    }
    public function getApproverProperty()
    {
        return ApplicantIncompletDeatil::query()
            ->where('next_level_request_id', 2)
            ->when(
                isset($this->filterCode),
                fn($q) =>
                $q->where('incomplet_type',  $this->filterCode)
            )
            ->whereHas('beneficiaryCommonList', function ($q) {
                foreach ($this->filter_condition as $col => $val) {
                    if ($col !== 'incomplete_type') {
                        $q->where($col, $val);
                    }
                }
            })
            ->select('application_id')
            ->groupBy('application_id')
            ->get()
            ->count();
    }

    public function exportDistrictExcel($districtName, $type)
    {
        dd('ok');
    }
    public function render()
    {
        $this->dispatch('hideLoader');
        $districts = District::select('id', 'name')->orderBy('name')->get();

        $rows = $districts->map(function ($district) {

            if (
                isset($this->filter_condition['district_id']) &&
                $district->id == $this->filter_condition['district_id']
            ) {
                return (object)[
                    'district' => $district->name,
                    'pending'  => $this->verifierPending,
                    'verifier' => $this->verifier,
                    'approve'  => $this->approver,
                    'active'   => true
                ];
            }

            return (object)[
                'district' => $district->name,
                'pending'  => 0,
                'verifier' => 0,
                'approve'  => 0,
                'active'   => false
            ];
        });
       

        $totals = [
            'pending'  => $rows->sum('pending'),
            'verifier' => $rows->sum('verifier'),
            'approve'  => $rows->sum('approve'),
            'grand'    => $rows->sum('pending') + $rows->sum('verifier') + $rows->sum('approve'),
        ];



        return view('livewire.incomplete-mis-report-table', [
            'rows' => $rows,
            'totals' => $totals
        ]);
    }
}
