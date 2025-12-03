<?php

namespace App\Livewire;

use App\Models\ApplicantIncompletDeatil;
use App\Models\District;
use Livewire\Component;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\IncompleteExport;

class IncompleteMisReportTable extends Component
{
    public array $filter_condition = [];
    public $incomplete_type = null;
    protected $listeners = [
        'filterIncompleteType' => 'applyIncompleteFilter'
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

    public function applyIncompleteFilter($value)
    {
        $this->incomplete_type = $value;

        if ($value) {
            $this->filter_condition['incomplete_type'] = $value;
        } else {
            unset($this->filter_condition['incomplete_type']);
        }

        $this->dispatch('$refresh');
    }

    public function getVerifierPendingProperty()
    {
        return ApplicantIncompletDeatil::query()
            ->whereNull('next_level_request_id')
            ->when(
                isset($this->filter_condition['incomplete_type']),
                fn($q) =>
                $q->where('incomplet_type', $this->filter_condition['incomplete_type'])
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
                isset($this->filter_condition['incomplete_type']),
                fn($q) =>
                $q->where('incomplet_type', $this->filter_condition['incomplete_type'])
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
                isset($this->filter_condition['incomplete_type']),
                fn($q) =>
                $q->where('incomplet_type', $this->filter_condition['incomplete_type'])
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
