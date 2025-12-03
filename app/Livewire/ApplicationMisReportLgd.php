<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\BeneficiaryCommonList;
use App\Models\District;
use App\Models\Block;
use App\Models\Subdivision;
use App\Models\Municipality;
use App\Models\Ward;
use App\Models\Panchayat;
use Illuminate\Support\Facades\Crypt;

class ApplicationMisReportLgd extends Component
{
    // incoming filters (from child)
    public $district_id;
    public $rural_urban;
    public $blockurban;   // LGD id or code from UI (or FK)
    public $gp_ward;      // LGD id or code from UI
    public $sub_div;      // subdivision_id

    // session defaults (decrypted into FK keys by mount)
    public array $filter_condition = [];

    // UI
    public int $page = 1;
    public int $perPage = 20;
    public $sortField = 'label';
    public $sortDirection = 'asc';

    protected $listeners = [
        'filtersApplied'    => 'filtersApplied',
        'resetChildFilters' => 'resetChildFilters',
        'commonTableSort'   => 'onCommonTableSort',
    ];

    /**
     * mount: read session lgd_session and set FK filter_condition where available
     */
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
    }

    /**
     * Child emits filtersApplied -> map to internal props and set filter_condition codes/FKs.
     * Rules:
     *  - If subdivision present (session or selection) AND blockurban selected => treat blockurban as cd_block_muni_id (code)
     *  - gp_ward maps to cd_gp_ward_id (code)
     *  - district selection prefers FK district_id
     */
    public function filtersApplied($filters)
    {
        // UI props
        $this->district_id = $filters['district_id'] ?? null;
        $this->rural_urban = $filters['rural_urban'] ?? null;
        $this->blockurban  = $filters['blockurban'] ?? null;
        $this->gp_ward     = $filters['gp_ward'] ?? null;
        $this->sub_div     = $filters['subdivision_id'] ?? null;

        // reset child-supplied code filters so we set deterministically
        unset($this->filter_condition['cd_block_muni_id'], $this->filter_condition['cd_gp_ward_id']);

        $subdivisionPresent = !empty($this->sub_div) || !empty($this->filter_condition['sub_division_id']);

        if ($subdivisionPresent && !empty($this->blockurban)) {
            // in subdivision context treat blockurban as code
            $this->filter_condition['cd_block_muni_id'] = $this->blockurban;
            unset($this->filter_condition['block_id']);
        } else {
            // treat as FK block_id if no subdivision context
            if (!empty($this->blockurban)) {
                $this->filter_condition['block_id'] = $this->blockurban;
            } else {
                unset($this->filter_condition['block_id']);
            }
            unset($this->filter_condition['cd_block_muni_id']);
        }

        // gp_ward -> cd_gp_ward_id (codes)
        if (!empty($this->gp_ward)) {
            $this->filter_condition['cd_gp_ward_id'] = $this->gp_ward;
        } else {
            unset($this->filter_condition['cd_gp_ward_id']);
        }

        // district explicit selection overrides session cd_district_id
        if (!empty($this->district_id)) {
            $this->filter_condition['district_id'] = $this->district_id;
            unset($this->filter_condition['cd_district_id']);
        }

        if (!empty($this->sub_div)) {
            $this->filter_condition['sub_division_id'] = $this->sub_div;
            unset($this->filter_condition['cd_block_muni_id']);
        }

        $this->page = 1;
        $this->dispatch('refreshReport');
    }

    /**
     * Reset child filters but keep session-enforced FK filters.
     */
    public function resetChildFilters()
    {
        $this->district_id = $this->rural_urban = $this->blockurban = $this->gp_ward = $this->sub_div = null;
        unset($this->filter_condition['cd_block_muni_id'], $this->filter_condition['cd_gp_ward_id']);
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
     * Build merged filters with precedence and avoid conflicting keys.
     */
    protected function buildMergedFilters(): array
    {
        $merged = array_merge($this->filter_condition, [
            'district_id' => $this->district_id ?? null,
            'block_id' => $this->blockurban ?? null,
            'sub_division_id' => $this->sub_div ?? null,
        ]);

        // remove empty values
        $merged = array_filter($merged, fn($v) => $v !== null && $v !== '');

        // if subdivision present and cd_block_muni_id exists, remove block_id to avoid contradiction
        if (!empty($merged['sub_division_id']) && !empty($merged['cd_block_muni_id'])) {
            unset($merged['block_id']);
        }

        // prefer explicit FK district if conflict with cd_district_id
        if (!empty($merged['district_id']) && !empty($merged['cd_district_id']) && $merged['district_id'] != $merged['cd_district_id']) {
            unset($merged['cd_district_id']);
        }

        return $merged;
    }

    /**
     * Get master group ids (ids or cd codes) for the given grouping column.
     * Important: panchayat/ward master tables are expected to store LGD code in cd_gp_ward_id as you said.
     */
    protected function getMasterGroupIds(string $groupCol, array $filters = []): array
    {
        switch ($groupCol) {
            case 'district_id':
                return District::orderBy('name')->pluck('id')->map(fn($v)=>(int)$v)->all();

            case 'block_id':
                if (!empty($filters['district_id'])) {
                    return Block::where('district_id', $filters['district_id'])->orderBy('name')->pluck('id')->map(fn($v)=>(int)$v)->all();
                }
                if (!empty($filters['cd_district_id'])) {
                    return Block::where('district_id', $filters['cd_district_id'])->orderBy('name')->pluck('id')->map(fn($v)=>(int)$v)->all();
                }
                return Block::orderBy('name')->pluck('id')->map(fn($v)=>(int)$v)->all();

            case 'sub_division_id':
                if (!empty($filters['district_id'])) {
                    return Subdivision::where('district_id', $filters['district_id'])->orderBy('name')->pluck('id')->map(fn($v)=>(int)$v)->all();
                }
                if (!empty($filters['cd_district_id'])) {
                    return Subdivision::where('district_id', $filters['cd_district_id'])->orderBy('name')->pluck('id')->map(fn($v)=>(int)$v)->all();
                }
                return Subdivision::orderBy('name')->pluck('id')->map(fn($v)=>(int)$v)->all();

            case 'municipality_id':
                if (!empty($filters['sub_division_id'])) {
                    return Municipality::where('subdivision_id', $filters['sub_division_id'])->orderBy('name')->pluck('id')->map(fn($v)=>(int)$v)->all();
                }
                if (!empty($filters['cd_block_muni_id'])) {
                    $block = Block::where('cd_block_muni_id', $filters['cd_block_muni_id'])
                                  ->orWhere('code', $filters['cd_block_muni_id'])
                                  ->first();
                    if ($block) {
                        return Municipality::where('block_id', $block->id)->orderBy('name')->pluck('id')->map(fn($v)=>(int)$v)->all();
                    }
                    $mids = BeneficiaryCommonList::where('cd_block_muni_id', $filters['cd_block_muni_id'])
                        ->whereNotNull('municipality_id')
                        ->distinct()
                        ->pluck('municipality_id')
                        ->filter()
                        ->map(fn($v)=>(int)$v)
                        ->all();
                    if (!empty($mids)) return $mids;
                }
                return Municipality::orderBy('name')->pluck('id')->map(fn($v)=>(int)$v)->all();

            case 'panchayat_id':
                // 1) If FK block_id present -> return all panchayats belonging to that block (preferred)
                if (!empty($filters['block_id'])) {
                    return Panchayat::where('block_id', $filters['block_id'])->orderBy('name')->pluck('id')->map(fn($v)=>(int)$v)->all();
                }

                // 2) If cd_block_muni_id present, try map code -> block -> panchayats
                if (!empty($filters['cd_block_muni_id'])) {
                    $block = Block::where('cd_block_muni_id', $filters['cd_block_muni_id'])
                                  ->orWhere('code', $filters['cd_block_muni_id'])
                                  ->first();
                    if ($block) {
                        return Panchayat::where('block_id', $block->id)->orderBy('name')->pluck('id')->map(fn($v)=>(int)$v)->all();
                    }

                    // 3) Try map cd_gp_ward_id codes present in beneficiary rows to panchayat ids via panchayat.cd_gp_ward_id
                    $cdCodes = BeneficiaryCommonList::where('cd_block_muni_id', $filters['cd_block_muni_id'])
                        ->whereNotNull('cd_gp_ward_id')->distinct()
                        ->pluck('cd_gp_ward_id')->filter()->unique()->values()->all();

                    if (!empty($cdCodes)) {
                        $mapped = Panchayat::whereIn('cd_gp_ward_id', $cdCodes)->orderBy('name')->pluck('id')->map(fn($v)=>(int)$v)->all();
                        if (!empty($mapped)) return $mapped;

                        // fallback: return cd codes themselves (grouping by cd_gp_ward_id will be used)
                        return array_map('intval', $cdCodes);
                    }

                    // 4) fallback: return any panchayat ids present in beneficiary rows for the block code
                    $pids = BeneficiaryCommonList::where('cd_block_muni_id', $filters['cd_block_muni_id'])
                        ->whereNotNull('panchayat_id')->distinct()->pluck('panchayat_id')->filter()->map(fn($v)=>(int)$v)->all();
                    if (!empty($pids)) return $pids;
                }

                // 5) global fallback: return all panchayats master list
                return Panchayat::orderBy('name')->pluck('id')->map(fn($v)=>(int)$v)->all();

            case 'ward_id':
                if (!empty($filters['municipality_id'])) {
                    return Ward::where('municipality_id', $filters['municipality_id'])->orderBy('name')->pluck('id')->map(fn($v)=>(int)$v)->all();
                }

                if (!empty($filters['cd_block_muni_id'])) {
                    $block = Block::where('cd_block_muni_id', $filters['cd_block_muni_id'])
                                  ->orWhere('code', $filters['cd_block_muni_id'])
                                  ->first();
                    if ($block) {
                        $mids = Municipality::where('block_id', $block->id)->pluck('id')->map(fn($v)=>(int)$v)->all();
                        if (!empty($mids)) {
                            return Ward::whereIn('municipality_id', $mids)->orderBy('name')->pluck('id')->map(fn($v)=>(int)$v)->all();
                        }
                    }

                    $cdCodes = BeneficiaryCommonList::where('cd_block_muni_id', $filters['cd_block_muni_id'])
                        ->whereNotNull('cd_gp_ward_id')->distinct()->pluck('cd_gp_ward_id')->filter()->unique()->values()->all();
                    if (!empty($cdCodes)) {
                        $mapped = Ward::whereIn('cd_gp_ward_id', $cdCodes)->orderBy('name')->pluck('id')->map(fn($v)=>(int)$v)->all();
                        if (!empty($mapped)) return $mapped;
                        return array_map('intval', $cdCodes);
                    }
                }

                return Ward::orderBy('name')->pluck('id')->map(fn($v)=>(int)$v)->all();

            case 'cd_gp_ward_id':
                $q = BeneficiaryCommonList::query();
                foreach ($filters as $c => $v) if ($v !== null && $v !== '') $q->where($c, $v);
                return $q->whereNotNull('cd_gp_ward_id')->distinct()->pluck('cd_gp_ward_id')->map(fn($v)=>(int)$v)->all();

            case 'cd_block_muni_id':
                $q = BeneficiaryCommonList::query();
                foreach ($filters as $c => $v) if ($v !== null && $v !== '') $q->where($c, $v);
                return $q->whereNotNull('cd_block_muni_id')->distinct()->pluck('cd_block_muni_id')->map(fn($v)=>(int)$v)->all();

            default:
                return [];
        }
    }

    /**
     * Aggregation using Eloquent only: pluck + countBy.
     * Ensures master groups are included (zero counts).
     */
    protected function aggregatedCountsByGroupNoRaw(string $groupCol, array $filters = []): array
    {
        $allowed = [
            'district_id','block_id','sub_division_id',
            'municipality_id','ward_id','panchayat_id',
            'cd_district_id','cd_rural_urban_id','cd_block_muni_id','cd_gp_ward_id'
        ];
        if (!in_array($groupCol, $allowed)) throw new \InvalidArgumentException("Invalid group column: {$groupCol}");

        $base = BeneficiaryCommonList::query();
        foreach ($filters as $c => $v) {
            if ($v !== null && $v !== '') $base->where($c, $v);
        }

        // distinct values present in data
        $groupIdsFromData = (clone $base)->whereNotNull($groupCol)->distinct()->pluck($groupCol)->filter()->unique()->values()->all();

        // master ids/codes for group
        $masterIds = $this->getMasterGroupIds($groupCol, $filters);

        // union master + data so groups with zero appear
        $groupIds = array_values(array_unique(array_merge($masterIds, $groupIdsFromData)));

        // special fallback: if grouping by panchayat_id but beneficiaries only have cd codes -> switch to cd_gp_ward_id grouping
        if ($groupCol === 'panchayat_id') {
            $hasPanchayatFk = (clone $base)->whereNotNull('panchayat_id')->whereIn('panchayat_id', $groupIds)->exists();
            $hasCdGpWard = (clone $base)->whereNotNull('cd_gp_ward_id')->exists();
            if (!$hasPanchayatFk && $hasCdGpWard) {
                return $this->aggregatedCountsByGroupNoRaw('cd_gp_ward_id', $filters);
            }
        }

        if (empty($groupIds)) return [];

        $pluckCount = function(callable $cond) use ($base, $groupCol, $groupIds) {
            $q = (clone $base)->whereIn($groupCol, $groupIds);
            $cond($q);
            return $q->pluck($groupCol)->countBy()->all();
        };

        // your mapping: pending=21, verified=22, approved=144, rejected=is_reject OR next_level_role_id=145
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
     * Determine group column based on role and merged filters.
     * Includes Operator handling: if user has session block -> show panchayat/ward for that block.
     */
    protected function determineGroupColumn(): array
    {
        $user = auth()->user();

        $merged = $this->buildMergedFilters();

        // session hints
        $sessBlockFK = $this->filter_condition['block_id'] ?? $merged['block_id'] ?? null;
        $sessBlockCode = $this->filter_condition['cd_block_muni_id'] ?? $merged['cd_block_muni_id'] ?? null;
        $sessDistrictFK = $this->filter_condition['district_id'] ?? $merged['district_id'] ?? null;

        $districtSelected = $this->district_id ?? ($merged['district_id'] ?? null) ?? null;

        // Operator: if session has block (FK or code) -> panchayat/ward
        if ($user->hasRole('Operator')) {
            if (!empty($sessBlockFK) || !empty($sessBlockCode)) {
                if (!empty($this->blockurban)) {
                    $r = (int) ($this->rural_urban ?? $merged['cd_rural_urban_id'] ?? 0);
                    return ['group' => ($r === 2 ? 'panchayat_id' : 'ward_id'), 'filters' => $merged];
                }
                $r = (int) ($this->rural_urban ?? $merged['cd_rural_urban_id'] ?? 0);
                return ['group' => ($r === 1 ? 'ward_id' : 'panchayat_id'), 'filters' => $merged];
            }
        }

        // Admin without district -> district wise
        if ($user->hasRole('Admin') && empty($merged['district_id'])) {
            return ['group' => 'district_id', 'filters' => $merged];
        }

        if ($districtSelected) {
            // if a block is specifically selected -> panchayat/ward
            if (!empty($this->blockurban)) {
                $r = (int) ($this->rural_urban ?? $merged['cd_rural_urban_id'] ?? 0);
                $groupCol = $r === 2 ? 'panchayat_id' : 'ward_id';
                return ['group' => $groupCol, 'filters' => $merged];
            }

            if ($this->rural_urban) {
                if ((int)$this->rural_urban === 2) return ['group' => 'block_id', 'filters' => $merged];
                if ((int)$this->rural_urban === 1) return ['group' => 'sub_division_id', 'filters' => $merged];
            }

            if ($this->sub_div) return ['group' => 'municipality_id', 'filters' => $merged];

            if ($this->gp_ward && ($this->rural_urban == 1 || $this->sub_div)) return ['group' => 'ward_id', 'filters' => $merged];

            return ['group' => 'block_id', 'filters' => $merged];
        }

        // Approver with session district -> block level
        if ($user->hasRole('Approver')) {
            $sess = $this->filter_condition['district_id'] ?? null;
            if ($sess && empty($this->district_id)) return ['group' => 'block_id', 'filters' => $merged];
        }

        // Verifier session restrictions
        if ($user->hasRole('Verifier')) {
            $sessBlock = $this->filter_condition['block_id'] ?? null;
            $sessSub   = $this->filter_condition['sub_division_id'] ?? null;
            if ($sessBlock) return ['group' => 'panchayat_id', 'filters' => $merged];
            if ($sessSub)   return ['group' => 'municipality_id', 'filters' => $merged];
        }

        // fallback
        return ['group' => 'district_id', 'filters' => $merged];
    }

    /**
     * Resolve labels for group ids or codes.
     * For cd_gp_ward_id codes try mapping to master Panchayat/Ward name using cd_gp_ward_id field.
     */
    protected function resolveLabels(string $groupCol, array $ids): array
    {
        $map = [
            'district_id' => District::class,
            'block_id' => Block::class,
            'sub_division_id' => Subdivision::class,
            'municipality_id' => Municipality::class,
            'ward_id' => Ward::class,
            'panchayat_id' => Panchayat::class,
        ];

        $labels = [];
        $ids = array_values($ids);

        if ($groupCol === 'cd_gp_ward_id') {
            // try Panchayat mapping
            $names = Panchayat::whereIn('cd_gp_ward_id', $ids)->pluck('name', 'cd_gp_ward_id')->all();
            foreach ($ids as $id) {
                if (!empty($names[$id])) {
                    $labels[$id] = $names[$id];
                } else {
                    $w = Ward::where('cd_gp_ward_id', $id)->pluck('name','cd_gp_ward_id')->first();
                    $labels[$id] = $w ?: ("GP/Ward {$id}");
                }
            }
            return $labels;
        }

        if (str_starts_with($groupCol, 'cd_')) {
            foreach ($ids as $id) $labels[$id] = ucfirst(str_replace('cd_', '', $groupCol)) . " {$id}";
            return $labels;
        }

        $model = $map[$groupCol] ?? null;
        if ($model && class_exists($model) && !empty($ids)) {
            try {
                $names = $model::whereIn('id', $ids)->pluck('name','id')->all();
                foreach ($ids as $id) $labels[$id] = $names[$id] ?? ucfirst($groupCol) . " {$id}";
                return $labels;
            } catch (\Throwable $e) {}
        }

        foreach ($ids as $id) $labels[$id] = ucfirst($groupCol) . " {$id}";
        return $labels;
    }

    /**
     * detectRecordOrigin helper (unchanged behavior)
     */
    protected function detectRecordOrigin($row): array
    {
        $getName = function ($modelClass, $id, $fallback) {
            if (!$modelClass || !$id) return $fallback;
            if (!class_exists($modelClass)) return $fallback;
            try {
                $m = $modelClass::find($id);
                if ($m && isset($m->name)) return $m->name;
            } catch (\Throwable $e) {}
            return $fallback;
        };

        if (!empty($row->panchayat_id)) {
            return ['level'=>'panchayat','key'=>$row->panchayat_id,'label'=>$getName(Panchayat::class,$row->panchayat_id,"Panchayat {$row->panchayat_id}"),'rural_urban'=>$row->cd_rural_urban_id ?? null];
        }
        if (!empty($row->ward_id)) {
            return ['level'=>'ward','key'=>$row->ward_id,'label'=>$getName(Ward::class,$row->ward_id,"Ward {$row->ward_id}"),'rural_urban'=>$row->cd_rural_urban_id ?? null];
        }
        if (!empty($row->municipality_id)) {
            return ['level'=>'municipality','key'=>$row->municipality_id,'label'=>$getName(Municipality::class,$row->municipality_id,"Municipality {$row->municipality_id}"),'rural_urban'=>$row->cd_rural_urban_id ?? null];
        }
        if (!empty($row->sub_division_id)) {
            return ['level'=>'subdivision','key'=>$row->sub_division_id,'label'=>$getName(Subdivision::class,$row->sub_division_id,"Subdivision {$row->sub_division_id}"),'rural_urban'=>$row->cd_rural_urban_id ?? null];
        }
        if (!empty($row->block_id)) {
            return ['level'=>'block','key'=>$row->block_id,'label'=>$getName(Block::class,$row->block_id,"Block {$row->block_id}"),'rural_urban'=>$row->cd_rural_urban_id ?? null];
        }
        if (!empty($row->district_id)) {
            return ['level'=>'district','key'=>$row->district_id,'label'=>$getName(District::class,$row->district_id,"District {$row->district_id}"),'rural_urban'=>$row->cd_rural_urban_id ?? null];
        }

        if (!empty($row->cd_gp_ward_id)) return ['level'=>'cd_gp_ward','key'=>$row->cd_gp_ward_id,'label'=>"GP/Ward code {$row->cd_gp_ward_id}",'rural_urban'=>$row->cd_rural_urban_id ?? null];
        if (!empty($row->cd_block_muni_id)) return ['level'=>'cd_block','key'=>$row->cd_block_muni_id,'label'=>"Block/Muni code {$row->cd_block_muni_id}",'rural_urban'=>$row->cd_rural_urban_id ?? null];
        if (!empty($row->cd_district_id)) return ['level'=>'cd_district','key'=>$row->cd_district_id,'label'=>"District code {$row->cd_district_id}",'rural_urban'=>$row->cd_rural_urban_id ?? null];

        return ['level'=>'unknown','key'=>null,'label'=>'Unknown','rural_urban'=>$row->cd_rural_urban_id ?? null];
    }

    /**
     * render: build rows, totals, sorting and pagination
     */
    public function render()
    {
        $choice = $this->determineGroupColumn();
        $groupCol = $choice['group'];
        $filters  = $choice['filters'];

        $countsAssoc = $this->aggregatedCountsByGroupNoRaw($groupCol, $filters);

        // fallback: if panchayat grouping yields nothing but cd codes exist, group by cd_gp_ward_id
        if (empty($countsAssoc) && $groupCol === 'panchayat_id') {
            $countsAssoc = $this->aggregatedCountsByGroupNoRaw('cd_gp_ward_id', $filters);
            $groupCol = 'cd_gp_ward_id';
        }

        $ids = array_keys($countsAssoc);
        sort($ids, SORT_NUMERIC);
        $labels = $this->resolveLabels($groupCol, $ids);

        $rows = [];
        $totals = ['pending'=>0,'verified'=>0,'approved'=>0,'rejected'=>0,'total'=>0];
        foreach ($ids as $id) {
            $c = $countsAssoc[$id] ?? ['pending'=>0,'verified'=>0,'approved'=>0,'rejected'=>0,'total'=>0];
            $rows[] = [
                'id' => $id,
                'label' => $labels[$id] ?? ucfirst($groupCol) . " {$id}",
                'pending' => $c['pending'],
                'verified' => $c['verified'],
                'approved' => $c['approved'],
                'rejected' => $c['rejected'],
                'total' => $c['total'],
            ];
            foreach ($totals as $k => $_) $totals[$k] += $c[$k];
        }

        usort($rows, function($a,$b) {
            $f = $this->sortField;
            $dir = $this->sortDirection === 'desc' ? -1 : 1;
            if (!isset($a[$f]) || !isset($b[$f])) return 0;
            if ($a[$f] == $b[$f]) return 0;
            return ($a[$f] < $b[$f]) ? -1 * $dir : 1 * $dir;
        });

        $totalRows = count($rows);
        $start = ($this->page - 1) * $this->perPage;
        $paged = array_slice($rows, $start, $this->perPage);

        $columns = [
            ['label' => ucfirst(str_replace('_', ' ', $groupCol)), 'field' => 'label', 'sortable' => true],
            ['label' => 'Pending', 'field' => 'pending', 'sortable' => true],
            ['label' => 'Verified', 'field' => 'verified', 'sortable' => true],
            ['label' => 'Approved', 'field' => 'approved', 'sortable' => true],
            ['label' => 'Rejected', 'field' => 'rejected', 'sortable' => true],
            ['label' => 'Total', 'field' => 'total', 'sortable' => true],
        ];

        $totalsForTable = array_merge(['label' => 'TOTAL'], $totals);

        return view('livewire.application-mis-report-lgd', [
            'rows' => $paged,
            'columns' => $columns,
            'page' => $this->page,
            'perPage' => $this->perPage,
            'totalRows' => $totalRows,
            'totals' => $totalsForTable,
        ]);
    }
}
