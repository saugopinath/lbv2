<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\BeneficiaryCommonList;
use App\Models\District;
use App\Models\Block;
use App\Models\Subdivision;
use App\Models\Panchayat;
use Illuminate\Support\Facades\Crypt;

class ApplicationMisReportLgd extends Component
{
    // incoming filters (from child)
    public $district_id;
    public $rural_urban;
    public $blockurban;   // may be FK block_id or cd_block_muni_id depending on context
    public $gp_ward;      // cd_gp_ward_id (code) from UI
    public $sub_div;      // subdivision_id

    // session defaults (decrypted into FK keys by mount)
    public array $filter_condition = [];

    // UI lists
    public $blocks = [];
    public $subdivisions = [];

    // pagination & sorting
    public int $page = 1;
    public int $perPage = 20;
    public $sortField = 'label';
    public $sortDirection = 'asc';

    protected $listeners = [
        'filtersApplied'    => 'filtersApplied',
        'resetChildFilters' => 'resetChildFilters',
        'commonTableSort'   => 'onCommonTableSort',
    ];

    public function mount()
    {
        $lgd = session('lgd_session') ?? [];

        try {
            if (!empty($lgd['district_id'])) {
                $this->filter_condition['district_id'] = (int) Crypt::decryptString($lgd['district_id']);
            }
        } catch (\Throwable $e) {}

        try {
            if (!empty($lgd['block_id'])) {
                $this->filter_condition['block_id'] = (int) Crypt::decryptString($lgd['block_id']);
            }
        } catch (\Throwable $e) {}

        try {
            if (!empty($lgd['subdivision_id'])) {
                $this->filter_condition['sub_division_id'] = (int) Crypt::decryptString($lgd['subdivision_id']);
            }
        } catch (\Throwable $e) {}

        $sessionDistrict = $this->filter_condition['district_id'] ?? null;
        if ($sessionDistrict) {
            $this->blocks = Block::where('district_id', $sessionDistrict)->orderBy('name')->get();
            $this->subdivisions = Subdivision::where('district_id', $sessionDistrict)->orderBy('name')->get();
        }
    }

    public function filtersApplied($filters)
    {
        $this->district_id = $filters['district_id'] ?? null;
        $this->rural_urban = $filters['rural_urban'] ?? null;
        $this->blockurban  = $filters['blockurban'] ?? null;
        $this->gp_ward     = $filters['gp_ward'] ?? null;
        $this->sub_div     = $filters['subdivision_id'] ?? null;

        // reset cd keys to set deterministically
        unset($this->filter_condition['cd_block_muni_id'], $this->filter_condition['cd_gp_ward_id']);

        $subdivisionPresent = !empty($this->sub_div) || !empty($this->filter_condition['sub_division_id']);

        if ($subdivisionPresent && !empty($this->blockurban)) {
            // treat blockurban as cd_block_muni_id inside subdivision context
            $this->filter_condition['cd_block_muni_id'] = $this->blockurban;
            unset($this->filter_condition['block_id']);
        } else {
            if (!empty($this->blockurban)) {
                $this->filter_condition['block_id'] = $this->blockurban;
            } else {
                unset($this->filter_condition['block_id']);
            }
            unset($this->filter_condition['cd_block_muni_id']);
        }

        if (!empty($this->gp_ward)) {
            $this->filter_condition['cd_gp_ward_id'] = $this->gp_ward;
        } else {
            unset($this->filter_condition['cd_gp_ward_id']);
        }

        if (!empty($this->district_id)) {
            $this->filter_condition['district_id'] = $this->district_id;
            $this->blocks = Block::where('district_id', $this->district_id)->orderBy('name')->get();
            $this->subdivisions = Subdivision::where('district_id', $this->district_id)->orderBy('name')->get();
        } else {
            $sessionDistrict = $this->filter_condition['district_id'] ?? null;
            if ($sessionDistrict && empty($this->rural_urban)) {
                $this->blocks = Block::where('district_id', $sessionDistrict)->orderBy('name')->get();
                $this->subdivisions = Subdivision::where('district_id', $sessionDistrict)->orderBy('name')->get();
            }
        }

        if (!empty($this->sub_div)) {
            $this->filter_condition['sub_division_id'] = $this->sub_div;
            unset($this->filter_condition['cd_block_muni_id']);
        }

        $this->page = 1;
        $this->dispatch('refreshReport');
    }

    public function resetChildFilters()
    {
        $this->district_id = $this->rural_urban = $this->blockurban = $this->gp_ward = $this->sub_div = null;
        unset($this->filter_condition['cd_block_muni_id'], $this->filter_condition['cd_gp_ward_id']);

        $sessionDistrict = $this->filter_condition['district_id'] ?? null;
        if ($sessionDistrict) {
            $this->blocks = Block::where('district_id', $sessionDistrict)->orderBy('name')->get();
            $this->subdivisions = Subdivision::where('district_id', $sessionDistrict)->orderBy('name')->get();
        }

        $this->page = 1;
        $this->dispatch('refreshReport');
    }

    public function onCommonTableSort($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * build merged filters: keep session values unless UI explicitly supplies them
     */
    protected function buildMergedFilters(): array
    {
        $merged = $this->filter_condition;

        $uiCandidates = [
            'district_id'     => $this->district_id ?? null,
            'block_id'        => $this->blockurban ?? null,
            'sub_division_id' => $this->sub_div ?? null,
        ];

        foreach ($uiCandidates as $k => $v) {
            if ($v !== null && $v !== '') {
                $merged[$k] = $v;
            }
        }

        $merged = array_filter($merged, fn($v) => $v !== null && $v !== '');

        if (!empty($merged['sub_division_id']) && !empty($merged['cd_block_muni_id'])) {
            unset($merged['block_id']);
        }

        if (!empty($merged['district_id']) && !empty($merged['cd_district_id']) && $merged['district_id'] != $merged['cd_district_id']) {
            unset($merged['cd_district_id']);
        }

        return $merged;
    }

    /**
     * master ids for grouping columns
     * for cd_gp_ward_id: when block/district present use panchayat.lgd_code (master list)
     */
    protected function getMasterGroupIds(string $groupCol, array $filters = []): array
    {
        switch ($groupCol) {
            case 'district_id':
                return District::orderBy('name')->pluck('id')->map(fn($v)=> (int)$v)->all();

            case 'block_id':
                if (!empty($filters['district_id'])) {
                    return Block::where('district_id', $filters['district_id'])->orderBy('name')->pluck('id')->map(fn($v)=>(int)$v)->all();
                }
                return Block::orderBy('name')->pluck('id')->map(fn($v)=>(int)$v)->all();

            case 'sub_division_id':
                if (!empty($filters['district_id'])) {
                    return Subdivision::where('district_id', $filters['district_id'])->orderBy('name')->pluck('id')->map(fn($v)=>(int)$v)->all();
                }
                return Subdivision::orderBy('name')->pluck('id')->map(fn($v)=>(int)$v)->all();

            case 'cd_block_muni_id':
                $q = BeneficiaryCommonList::query();
                foreach ($filters as $c => $v) if ($v !== null && $v !== '') $q->where($c, $v);
                return $q->whereNotNull('cd_block_muni_id')->distinct()->pluck('cd_block_muni_id')->map(fn($v)=>(int)$v)->all();

            case 'cd_gp_ward_id':
                // prefer Panchayat master list when block/district context is present
                if (!empty($filters['block_id']) || !empty($filters['district_id'])) {
                    $q = Panchayat::query();
                    if (!empty($filters['block_id'])) {
                        $q->where('block_id', $filters['block_id']);
                    } elseif (!empty($filters['district_id'])) {
                        $q->where('district_id', $filters['district_id']);
                    }
                    // use lgd_code as master code value (convert to int)
                    return $q->orderBy('name')->pluck('lgd_code')->filter()->map(fn($v)=>(int)$v)->all();
                }

                // fallback: derive codes from beneficiary_common_lists
                $q = BeneficiaryCommonList::query();
                foreach ($filters as $c => $v) if ($v !== null && $v !== '') $q->where($c, $v);
                return $q->whereNotNull('cd_gp_ward_id')->distinct()->pluck('cd_gp_ward_id')->map(fn($v)=>(int)$v)->all();

            default:
                return [];
        }
    }

    /**
     * aggregate counts without raw SQL
     */
    protected function aggregatedCountsByGroupNoRaw(string $groupCol, array $filters = []): array
    {
        $allowed = [
            'district_id','block_id','sub_division_id',
            'cd_block_muni_id','cd_gp_ward_id'
        ];
        if (!in_array($groupCol, $allowed)) throw new \InvalidArgumentException("Invalid group column: {$groupCol}");

        $base = BeneficiaryCommonList::query();
        foreach ($filters as $c => $v) {
            if ($v !== null && $v !== '') $base->where($c, $v);
        }

        $groupIdsFromData = (clone $base)->whereNotNull($groupCol)->distinct()->pluck($groupCol)->filter()->unique()->values()->all();

        $masterIds = $this->getMasterGroupIds($groupCol, $filters);

        // union to include zero-count groups from master
        $groupIds = array_values(array_unique(array_merge($masterIds, $groupIdsFromData)));

        if (empty($groupIds)) return [];

        $pluckCount = function(callable $cond) use ($base, $groupCol, $groupIds) {
            $q = (clone $base)->whereIn($groupCol, $groupIds);
            $cond($q);
            return $q->pluck($groupCol)->countBy()->all();
        };

        $pending  = $pluckCount(fn($q) => $q->where('next_level_role_id', 21));
        $verified = $pluckCount(fn($q) => $q->where('next_level_role_id', 22));
        $approved = $pluckCount(fn($q) => $q->where('next_level_role_id', 144));
        $rejected = $pluckCount(fn($q) => $q->where(fn($qq) => $qq->where('is_reject', true)->orWhere('next_level_role_id', 145)));
        $total    = (clone $base)->whereIn($groupCol, $groupIds)->pluck($groupCol)->countBy()->all();

        $out = [];
        foreach ($groupIds as $gid) {
            $k = (string)$gid;
            $out[(int)$gid] = [
                'pending'  => isset($pending[$k])  ? (int)$pending[$k]  : 0,
                'verified' => isset($verified[$k]) ? (int)$verified[$k] : 0,
                'approved' => isset($approved[$k]) ? (int)$approved[$k] : 0,
                'rejected' => isset($rejected[$k]) ? (int)$rejected[$k] : 0,
                'total'    => isset($total[$k])    ? (int)$total[$k]    : 0,
            ];
        }

        return $out;
    }

    /**
     * determine grouping column
     * Verifier now behaves like Operator for block context (cd_gp_ward_id)
     */
    protected function determineGroupColumn(): array
    {
        $user = auth()->user();
        $merged = $this->buildMergedFilters();

        $sessBlockFK    = $this->filter_condition['block_id'] ?? $merged['block_id'] ?? null;
        $sessDistrictFK = $this->filter_condition['district_id'] ?? $merged['district_id'] ?? null;
        $sessSubFK      = $this->filter_condition['sub_division_id'] ?? $merged['sub_division_id'] ?? null;

        // Operator with session block -> gp/ward codes
        if ($user->hasRole('Operator')) {
            if (!empty($sessBlockFK)) {
                return ['group' => 'cd_gp_ward_id', 'filters' => $merged];
            }
        }

        // Verifier: behave like Operator when session block present (show gp/panchayat codes)
        if ($user->hasRole('Verifier')) {
            if (!empty($sessBlockFK)) {
                return ['group' => 'cd_gp_ward_id', 'filters' => $merged];
            }
            if (!empty($sessSubFK)) {
                // If you want sub->cd_block_muni_id for verifier, change this, but currently use gp when block exists.
                return ['group' => 'cd_block_muni_id', 'filters' => $merged];
            }
        }

        // Approver-only combined view
        if ($user->hasRole('Approver') && !$user->hasRole('Admin')) {
            $sessDistrictPresent = !empty($sessDistrictFK);
            $hasSelectedDistrictUI = !empty($this->district_id);
            $hasRuralUrbanSelected = !empty($this->rural_urban);

            if ($sessDistrictPresent && !$hasSelectedDistrictUI && !$hasRuralUrbanSelected) {
                return ['group' => 'block_and_subdivision', 'filters' => $merged];
            }

            if ($sessDistrictPresent && !$hasSelectedDistrictUI) {
                return ['group' => 'block_id', 'filters' => $merged];
            }
        }

        // Admin without district -> district wise
        if ($user->hasRole('Admin') && empty($merged['district_id'])) {
            return ['group' => 'district_id', 'filters' => $merged];
        }

        // explicit district selection => drilldown behaviour
        if (!empty($this->district_id) || !empty($merged['district_id'])) {
            if (!empty($this->blockurban)) {
                return ['group' => 'cd_gp_ward_id', 'filters' => $merged];
            }

            if ($this->rural_urban) {
                if ((int)$this->rural_urban === 2) return ['group' => 'block_id', 'filters' => $merged];
                if ((int)$this->rural_urban === 1) return ['group' => 'sub_division_id', 'filters' => $merged];
            }

            if ($this->sub_div) {
                return ['group' => 'cd_block_muni_id', 'filters' => $merged];
            }

            return ['group' => 'block_and_subdivision', 'filters' => $merged];
        }

        // fallback -> district
        return ['group' => 'district_id', 'filters' => $merged];
    }

    /**
     * resolve labels; for cd_gp_ward_id map panchayat.lgd_code -> panchayat.name
     */
    protected function resolveLabels(string $groupCol, array $ids): array
    {
        $labels = [];
        $ids = array_values($ids);

        if ($groupCol === 'cd_gp_ward_id') {
            // map by panchayat.lgd_code
            $map = Panchayat::whereIn('lgd_code', $ids)->pluck('name','lgd_code')->all();
            foreach ($ids as $id) $labels[$id] = $map[$id] ?? "Gp_ward_id {$id}";
            return $labels;
        }

        if ($groupCol === 'district_id') {
            $names = District::whereIn('id', $ids)->pluck('name','id')->all();
            foreach ($ids as $id) $labels[$id] = $names[$id] ?? "District {$id}";
            return $labels;
        }

        if ($groupCol === 'block_id') {
            $names = Block::whereIn('id', $ids)->pluck('name','id')->all();
            foreach ($ids as $id) $labels[$id] = $names[$id] ?? "Block {$id}";
            return $labels;
        }

        if ($groupCol === 'sub_division_id') {
            $names = Subdivision::whereIn('id', $ids)->pluck('name','id')->all();
            foreach ($ids as $id) $labels[$id] = $names[$id] ?? "Subdivision {$id}";
            return $labels;
        }

        foreach ($ids as $id) $labels[$id] = ucfirst($groupCol) . " {$id}";
        return $labels;
    }

    public function render()
    {
        $choice = $this->determineGroupColumn();
        $groupCol = $choice['group'];
        $filters  = $choice['filters'];

        // Combined block + subdivision (Approver session district view)
        if ($groupCol === 'block_and_subdivision') {
            $blockIds = $this->getMasterGroupIds('block_id', $filters);
            $blockCounts = $this->aggregatedCountsByGroupNoRaw('block_id', $filters);

            $subIds = $this->getMasterGroupIds('sub_division_id', $filters);
            $subCounts = $this->aggregatedCountsByGroupNoRaw('sub_division_id', $filters);

            $rows = [];
            $totals = ['pending'=>0,'verified'=>0,'approved'=>0,'rejected'=>0,'total'=>0];

            $blockLabels = $this->resolveLabels('block_id', $blockIds);
            foreach ($blockIds as $id) {
                $c = $blockCounts[$id] ?? ['pending'=>0,'verified'=>0,'approved'=>0,'rejected'=>0,'total'=>0];
                $rows[] = ['id'=>"B-{$id}", 'label'=>($blockLabels[$id] ?? "Block {$id}"), 'pending'=>$c['pending'],'verified'=>$c['verified'],'approved'=>$c['approved'],'rejected'=>$c['rejected'],'total'=>$c['total']];
                foreach ($totals as $k => $_) $totals[$k] += $c[$k];
            }

            $subLabels = $this->resolveLabels('sub_division_id', $subIds);
            foreach ($subIds as $id) {
                $c = $subCounts[$id] ?? ['pending'=>0,'verified'=>0,'approved'=>0,'rejected'=>0,'total'=>0];
                $rows[] = ['id'=>"S-{$id}", 'label'=>($subLabels[$id] ?? "Subdivision {$id}"), 'pending'=>$c['pending'],'verified'=>$c['verified'],'approved'=>$c['approved'],'rejected'=>$c['rejected'],'total'=>$c['total']];
                foreach ($totals as $k => $_) $totals[$k] += $c[$k];
            }

            usort($rows, function($a,$b){
                $f = $this->sortField; $dir = $this->sortDirection === 'desc' ? -1 : 1;
                if (!isset($a[$f]) || !isset($b[$f])) return 0;
                if ($a[$f] == $b[$f]) return 0;
                return ($a[$f] < $b[$f]) ? -1 * $dir : 1 * $dir;
            });

            $totalRows = count($rows);
            $start = ($this->page - 1) * $this->perPage;
            $paged = array_slice($rows, $start, $this->perPage);

            $columns = [
                ['label'=>'Block / Subdivision','field'=>'label','sortable'=>true],
                ['label'=>'Pending','field'=>'pending','sortable'=>true],
                ['label'=>'Verified','field'=>'verified','sortable'=>true],
                ['label'=>'Approved','field'=>'approved','sortable'=>true],
                ['label'=>'Rejected','field'=>'rejected','sortable'=>true],
                ['label'=>'Total','field'=>'total','sortable'=>true],
            ];

            $totalsForTable = array_merge(['label'=>'TOTAL'], $totals);

            return view('livewire.application-mis-report-lgd', [
                'rows'=>$paged,'columns'=>$columns,'page'=>$this->page,'perPage'=>$this->perPage,'totalRows'=>$totalRows,'totals'=>$totalsForTable
            ]);
        }

        // Normal grouping
        $countsAssoc = $this->aggregatedCountsByGroupNoRaw($groupCol, $filters);

        $ids = array_keys($countsAssoc);
        sort($ids, SORT_NUMERIC);
        $labels = $this->resolveLabels($groupCol, $ids);

        $rows = [];
        $totals = ['pending'=>0,'verified'=>0,'approved'=>0,'rejected'=>0,'total'=>0];
        foreach ($ids as $id) {
            $c = $countsAssoc[$id] ?? ['pending'=>0,'verified'=>0,'approved'=>0,'rejected'=>0,'total'=>0];
            $rows[] = ['id'=>$id,'label'=>$labels[$id] ?? ($groupCol . " {$id}"),'pending'=>$c['pending'],'verified'=>$c['verified'],'approved'=>$c['approved'],'rejected'=>$c['rejected'],'total'=>$c['total']];
            foreach ($totals as $k => $_) $totals[$k] += $c[$k];
        }

        usort($rows, function($a,$b){
            $f = $this->sortField; $dir = $this->sortDirection === 'desc' ? -1 : 1;
            if (!isset($a[$f]) || !isset($b[$f])) return 0;
            if ($a[$f] == $b[$f]) return 0;
            return ($a[$f] < $b[$f]) ? -1 * $dir : 1 * $dir;
        });

        $totalRows = count($rows);
        $start = ($this->page - 1) * $this->perPage;
        $paged = array_slice($rows, $start, $this->perPage);

        $columns = [
            ['label'=>ucfirst(str_replace('_',' ',$groupCol)),'field'=>'label','sortable'=>true],
            ['label'=>'Pending','field'=>'pending','sortable'=>true],
            ['label'=>'Verified','field'=>'verified','sortable'=>true],
            ['label'=>'Approved','field'=>'approved','sortable'=>true],
            ['label'=>'Rejected','field'=>'rejected','sortable'=>true],
            ['label'=>'Total','field'=>'total','sortable'=>true],
        ];

        $totalsForTable = array_merge(['label'=>'TOTAL'], $totals);

        return view('livewire.application-mis-report-lgd', [
            'rows'=>$paged,'columns'=>$columns,'page'=>$this->page,'perPage'=>$this->perPage,'totalRows'=>$totalRows,'totals'=>$totalsForTable
        ]);
    }
}
