<?php

namespace App\Livewire;

use App\Helpers\CheckAuthHelper;
use App\Models\ApplicantIncompletDeatil;
use App\Models\District;
use Livewire\Component;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use App\Models\Block;
use App\Models\Municipality;
use App\Models\Panchayat;
use App\Models\Subdivision;

class IncompleteMisReportTable extends Component
{
    public array $filter_condition = [];
    public $incomplete_type = null;

    // protected $listeners = [
    //     'filterIncompleteType' => 'applyIncompleteFilter'
    // ];

    public $district_id, $rural_urban, $blockurban, $gp_ward, $selectedSubdivision;

    // protected $listeners = ['doSearch' => 'doSearch'];
    protected $listeners = [
        'doSearch' => 'updateFilters',
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

    // public function applyIncompleteFilter($value)
    // {
    //     $this->incomplete_type = $value;

    //     if ($value) {
    //         $this->filter_condition['incomplete_type'] = $value;
    //     } else {
    //         unset($this->filter_condition['incomplete_type']);
    //     }

    //     $this->dispatch('$refresh');
    // }

    public function updateFilters($filters)
    {
        // dd($filters);
        $this->district_id = $filters['district_id'] ?? null;
        $this->rural_urban = $filters['rural_urban'] ?? null;
        $this->selectedSubdivision = $filters['subdivision_id'] ?? null;
        $this->blockurban = $filters['blockurban'] ?? null;
        $this->gp_ward = $filters['gp_ward'] ?? null;
        $this->incomplete_type = $filters['incomplete_type'] ?? null;
    }

    /* ===========================================================
     * Count Helper — PURE ORM, NO RAW SQL
     * =========================================================== */
    public function getCountByCondition(array $conditions)
    {
        return ApplicantIncompletDeatil::query()
            ->whereHas('beneficiaryCommonList', function ($q) use ($conditions) {

                // Apply user filters
                foreach ($this->filter_condition as $col => $val) {
                    $q->where($col, $val);
                }

                // Apply component conditionsz
                foreach ($conditions as $key => $value) {

                    if ($key === 'status') {
                        // convert human status → next_level_request_id
                        $map = [
                            'pending'  => null,
                            'verifier' => 1,
                            'approver' => 2,
                        ];
                        $q->where('next_level_request_id', $map[$value]);
                    } else {
                        $q->where($key, $value);
                    }
                }
            })
            ->distinct('application_id')  // ORM safe
            ->count('application_id');     // ORM safe
    }

    /* ===========================================================
     * Computed Properties — No select(), No groupBy()
     * =========================================================== */
    public function getVerifierPendingProperty()
    {
        return $this->getCountByCondition(['status' => 'pending']);
    }

    public function getVerifierProperty()
    {
        return $this->getCountByCondition(['status' => 'verifier']);
    }

    public function getApproverProperty()
    {
        return $this->getCountByCondition(['status' => 'approver']);
    }

    /* ===========================================================
     * Render
     * =========================================================== */
    public function render()
    {
        $this->dispatch('hideLoader');
        $user = Auth::user();
        $rows = collect();
        $groupLabel = '';

        /* ===============================
     * ADMIN → DISTRICT WISE
     * =============================== */
        // if ($user->hasRole('Admin')) {
        if (CheckAuthHelper::isSuperAdmin()) {
            //   $districts = District::select('id', 'name')->orderBy('name')->get();
            //           dd($districts);
            $groupLabel = 'Districts';

            foreach (District::orderBy('name')->get() as $d) {

                $isActive = ($this->filter_condition['district_id'] ?? null) == $d->id;

                $rows->push((object)[
                    'label'    => $d->name,
                    'pending'  => $isActive ? $this->verifierPending : 0,
                    'verifier' => $isActive ? $this->verifier : 0,
                    'approve'  => $isActive ? $this->approver : 0,
                    'active'   => $isActive
                ]);
            }
        }

        /* ===============================
     * VERIFIER → Panchayat / Municipality
     * =============================== */
        //  elseif ($user->hasRole('Verifier')) {
        elseif (CheckAuthHelper::isCommmonVerifier()) {

            if (!empty($this->filter_condition['block_id'])) {
                $items = Panchayat::where('block_id', $this->filter_condition['block_id'])->get();
                $groupLabel = 'Panchayats';
                $key = 'panchayat_id';
            } else {
                $items = Municipality::where('subdivision_id', $this->filter_condition['sub_division_id'] ?? 0)->get();
                $groupLabel = 'Municipalities';
                $key = 'municipality_id';
            }

            foreach ($items as $i) {
                $rows->push((object)[
                    'label'    => $i->name,
                    'pending'  => $this->getCountByCondition([$key => $i->id, 'status' => 'pending']),
                    'verifier' => $this->getCountByCondition([$key => $i->id, 'status' => 'verifier']),
                    'approve'  => $this->getCountByCondition([$key => $i->id, 'status' => 'approver']),
                    'active'   => true
                ]);
            }
        }

        /* ===============================
     * APPROVER → BLOCK + MUNICIPALITY WISE
     * =============================== */
        // elseif ($user->hasRole('Approver')) {
        elseif (CheckAuthHelper::isCommonApprover()) {
            $groupLabel = 'Blocks / Municipalities';
            $district_id = $this->filter_condition['district_id'] ?? 0;

            /** 1️⃣ BLOCKS */
            $blocks = Block::where('district_id', $district_id)->get();

            foreach ($blocks as $b) {
                $rows->push((object)[
                    'type'     => 'Block',
                    'label'    => $b->name,
                    'pending'  => $this->getCountByCondition(['block_id' => $b->id, 'status' => 'pending']),
                    'verifier' => $this->getCountByCondition(['block_id' => $b->id, 'status' => 'verifier']),
                    'approve'  => $this->getCountByCondition(['block_id' => $b->id, 'status' => 'approver']),
                    'active'   => true
                ]);
            }

            /** 2️⃣ MUNICIPALITIES */
            $subdivisionIds = Subdivision::where('district_id', $district_id)->pluck('id');

            $municipalities = Municipality::whereIn('subdivision_id', $subdivisionIds)->get();

            foreach ($municipalities as $m) {
                $rows->push((object)[
                    'type'     => 'Municipality',
                    'label'    => $m->name,
                    'pending'  => $this->getCountByCondition(['municipality_id' => $m->id, 'status' => 'pending']),
                    'verifier' => $this->getCountByCondition(['municipality_id' => $m->id, 'status' => 'verifier']),
                    'approve'  => $this->getCountByCondition(['municipality_id' => $m->id, 'status' => 'approver']),
                    'active'   => true
                ]);
            }
        }

        /* ===============================
 * OPERATOR → ONLY PANCHAYATS OF THEIR BLOCK
 * =============================== */
        //  elseif ($user->hasRole('Operator')) {
        elseif (CheckAuthHelper::isCommonOperator()) {
            $groupLabel = 'Panchayats';

            $block_id = $this->filter_condition['block_id'] ?? 0;

            /** Fetch Panchayats under this block */
            $panchayats = Panchayat::where('block_id', $block_id)->get();

            foreach ($panchayats as $p) {
                $rows->push((object)[
                    'label'    => $p->name,
                    'pending'  => $this->getCountByCondition(['panchayat_id' => $p->id, 'status' => 'pending']),
                    'verifier' => $this->getCountByCondition(['panchayat_id' => $p->id, 'status' => 'verifier']),
                    'approve'  => $this->getCountByCondition(['panchayat_id' => $p->id, 'status' => 'approver']),
                    'active'   => true
                ]);
            }
        }

        /* ===============================
     * TOTALS
     * =============================== */
        $totals = [
            'pending'  => $rows->sum('pending'),
            'verifier' => $rows->sum('verifier'),
            'approve'  => $rows->sum('approve'),
            'grand'    => $rows->sum('pending') + $rows->sum('verifier') + $rows->sum('approve'),
        ];

        return view('livewire.incomplete-mis-report-table', [
            'rows'       => $rows,
            'totals'     => $totals,
            'groupLabel' => $groupLabel
        ]);
    }
}
