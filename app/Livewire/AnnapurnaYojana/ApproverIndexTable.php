<?php

namespace App\Livewire\AnnapurnaYojana;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class ApproverIndexTable extends Component
{
    use WithPagination;

    // ── LGD Filter values (populated by filtersApplied event) ──────────────
    public $district_id    = null;
    public $rural_urban    = null; // 1 = Urban · 2 = Rural
    public $subdivision_id = null;
    public $blockurban     = null; // block_id (Rural) or municipality_id (Urban)
    public $gp_ward        = null; // panchayat_id (Rural) or ward_id (Urban)

    // ── Other filters ───────────────────────────────────────────────────────
    public string $gender  = '';
    public string $search  = '';
    public int    $perPage = 15;

    // ── Modal action properties ─────────────────────────────────────────────
    public $showActionModal = false;
    public $selectedFamilyId = null;
    public $modalOpType = 'Approve'; // 'Approve', 'Revert'
    public $modalRemarks = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'gender' => ['except' => ''],
    ];

    // ── Listen to FilterLgdMaster dispatch ──────────────────────────────────
    #[On('filtersApplied')]
    public function filtersApplied(array $filters): void
    {
        $this->district_id    = $filters['district_id']    ?? null;
        $this->rural_urban    = $filters['rural_urban']    ?? null;
        $this->subdivision_id = $filters['subdivision_id'] ?? null;
        $this->blockurban     = $filters['blockurban']     ?? null;
        $this->gp_ward        = $filters['gp_ward']        ?? null;
        $this->resetPage();
    }

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingGender(): void { $this->resetPage(); }

    public function mount()
    {
        $this->ensureSchema();
    }

    /**
     * Run dynamic ALTER TABLE commands to ensure verification/approval remarks and document columns exist.
     */
    protected function ensureSchema(): void
    {
        try {
            $conn = DB::connection('pgsql_ay');

            // Columns to add to families
            $familiesCols = [
                'next_level_role_id' => 'SMALLINT DEFAULT 0',
                'is_reverted' => 'SMALLINT DEFAULT 0',
                'verification_datetime' => 'TIMESTAMP WITHOUT TIME ZONE',
                'approval_datetime' => 'TIMESTAMP WITHOUT TIME ZONE',
                'verification_remarks' => 'TEXT',
                'verification_doc_path' => 'TEXT',
                'approval_remarks' => 'TEXT',
                'approval_doc_path' => 'TEXT'
            ];

            foreach ($familiesCols as $colName => $colType) {
                $exists = $conn->selectOne("
                    SELECT 1 
                    FROM information_schema.columns 
                    WHERE table_schema = 'dbt_apy' 
                      AND table_name = 'families' 
                      AND column_name = ?
                ", [$colName]);

                if (!$exists) {
                    $conn->statement("ALTER TABLE dbt_apy.families ADD COLUMN {$colName} {$colType}");
                }
            }

            // Columns to add to family_members
            $membersCols = [
                'next_level_role_id' => 'SMALLINT DEFAULT 0',
                'is_reverted' => 'SMALLINT DEFAULT 0',
                'verification_datetime' => 'TIMESTAMP WITHOUT TIME ZONE',
                'approval_datetime' => 'TIMESTAMP WITHOUT TIME ZONE'
            ];

            foreach ($membersCols as $colName => $colType) {
                $exists = $conn->selectOne("
                    SELECT 1 
                    FROM information_schema.columns 
                    WHERE table_schema = 'dbt_apy' 
                      AND table_name = 'family_members' 
                      AND column_name = ?
                ", [$colName]);

                if (!$exists) {
                    $conn->statement("ALTER TABLE dbt_apy.family_members ADD COLUMN {$colName} {$colType}");
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Dynamic schema update failed on index: ' . $e->getMessage());
        }
    }

    public function resetFilters(): void
    {
        $this->search        = '';
        $this->gender        = '';
        $this->district_id   = null;
        $this->rural_urban   = null;
        $this->subdivision_id= null;
        $this->blockurban    = null;
        $this->gp_ward       = null;
        $this->resetPage();
        $this->dispatch('resetChildFilters');
    }

    /**
     * Build shared WHERE clause and bindings for Approver Pending Queue.
     */
    private function buildWhere(): array
    {
        $conditions = [
            'COALESCE(f.is_reverted, 0) = 0',
            'COALESCE(f.next_level_role_id, 0) = 50' // Verified applications pending approval
        ];
        $bindings   = [];

        if ($this->district_id) {
            $conditions[] = 'f.lgd_district_code = ?';
            $bindings[]   = $this->district_id;
        }

        if ($this->rural_urban == 1) {
            $conditions[] = "f.area_type ILIKE 'Urban'";
            if ($this->blockurban) {
                $conditions[] = 'f.ulb = ?';
                $bindings[]   = $this->blockurban;
            }
            if ($this->gp_ward) {
                $conditions[] = 'f.lgd_gp_ward_code = ?';
                $bindings[]   = $this->gp_ward;
            }
        } elseif ($this->rural_urban == 2) {
            $conditions[] = "f.area_type ILIKE 'Rural'";
            if ($this->blockurban) {
                $conditions[] = 'f.lgd_block_mc_code = ?';
                $bindings[]   = $this->blockurban;
            }
            if ($this->gp_ward) {
                $conditions[] = 'f.lgd_gp_ward_code = ?';
                $bindings[]   = $this->gp_ward;
            }
        }

        if ($this->gender !== '') {
            $conditions[] = 'fm.gender ILIKE ?';
            $bindings[]   = $this->gender;
        }

        if ($this->search !== '') {
            $conditions[] = '(CAST(f.application_id AS TEXT) ILIKE ?
                              OR fm.member_name ILIKE ?
                              OR fm.aadhaar_no  ILIKE ?
                              OR fm.mobile_no   ILIKE ?)';
            $term         = '%' . $this->search . '%';
            $bindings[]   = $term;
            $bindings[]   = $term;
            $bindings[]   = $term;
            $bindings[]   = $term;
        }

        return [implode(' AND ', $conditions), $bindings];
    }

    /**
     * Action Modal controls
     */
    public function openActionModal($familyId, $opType = 'Approve')
    {
        $this->selectedFamilyId = (int)$familyId;
        $this->modalOpType = $opType;
        $this->modalRemarks = '';
        $this->resetErrorBag();
        $this->showActionModal = true;
    }

    public function closeActionModal()
    {
        $this->showActionModal = false;
        $this->selectedFamilyId = null;
        $this->modalRemarks = '';
        $this->resetErrorBag();
    }

    /**
     * Handle modal action submission (Approve, Revert)
     */
    public function submitModalAction()
    {
        $rules = [
            'modalOpType'   => 'required|in:Approve,Revert',
        ];

        if ($this->modalOpType === 'Revert') {
            $rules['modalRemarks'] = 'required|string|min:5|max:1000';
        } else {
            $rules['modalRemarks'] = 'nullable|string|max:1000';
        }

        $this->validate($rules);

        try {
            if ($this->modalOpType === 'Approve') {
                DB::connection('pgsql_ay')->update("
                    UPDATE dbt_apy.families 
                    SET    next_level_role_id = 100, 
                           is_reverted = 0,
                           approval_datetime = NOW(), 
                           status = 'Approved',
                           approval_remarks = ?,
                           updated_at = NOW() 
                    WHERE  id = ?
                ", [$this->modalRemarks, $this->selectedFamilyId]);

                DB::connection('pgsql_ay')->update("
                    UPDATE dbt_apy.family_members 
                    SET    next_level_role_id = 100, 
                           is_reverted = 0,
                           approval_datetime = NOW() 
                    WHERE  family_id = ?
                ", [$this->selectedFamilyId]);

                session()->flash('success', 'Family application approved and finalized successfully.');
            } elseif ($this->modalOpType === 'Revert') {
                DB::connection('pgsql_ay')->update("
                    UPDATE dbt_apy.families 
                    SET    next_level_role_id = 0, 
                           is_reverted = 1,
                           approval_datetime = NOW(), 
                           status = 'Reverted',
                           approval_remarks = ?,
                           updated_at = NOW() 
                    WHERE  id = ?
                ", [$this->modalRemarks, $this->selectedFamilyId]);

                DB::connection('pgsql_ay')->update("
                    UPDATE dbt_apy.family_members 
                    SET    next_level_role_id = 0, 
                           is_reverted = 1,
                           approval_datetime = NOW() 
                    WHERE  family_id = ?
                ", [$this->selectedFamilyId]);

                session()->flash('success', 'Family application reverted back to operator.');
            }

            $this->closeActionModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to process application: ' . $e->getMessage());
        }
    }

    public function render()
    {
        [$where, $bindings] = $this->buildWhere();

        // ── 1. Count distinct families ───────────────────────────────────────
        $countSql = "
            SELECT COUNT(DISTINCT f.id) AS total
            FROM   dbt_apy.families f
            INNER  JOIN dbt_apy.family_members fm ON fm.family_id = f.id
            WHERE  {$where}
        ";

        $total = (int) DB::connection('pgsql_ay')
            ->selectOne($countSql, $bindings)
            ->total;

        // ── 2. Paginate family IDs ───────────────────────────────────────────
        $page   = $this->getPage();
        $offset = ($page - 1) * $this->perPage;

        $familyIdSql = "
            SELECT DISTINCT f.id AS family_id
            FROM   dbt_apy.families f
            INNER  JOIN dbt_apy.family_members fm ON fm.family_id = f.id
            WHERE  {$where}
            ORDER  BY f.id ASC
            LIMIT  ? OFFSET ?
        ";

        $familyIds = collect(
            DB::connection('pgsql_ay')
                ->select($familyIdSql, array_merge($bindings, [$this->perPage, $offset]))
        )->pluck('family_id')->toArray();

        // ── 3. Fetch all members for those families ──────────────────────────
        $families = collect();

        if (!empty($familyIds)) {
            $placeholders   = implode(',', array_fill(0, count($familyIds), '?'));
            $memberWhere    = "fm.family_id IN ({$placeholders})";
            $memberBindings = $familyIds;

            if ($this->gender !== '') {
                $memberWhere    .= ' AND fm.gender ILIKE ?';
                $memberBindings[] = $this->gender;
            }

            $membersSql = "
                SELECT
                    f.id                                 AS family_id,
                    CAST(f.application_id AS TEXT)       AS application_id,
                    f.area_type,
                    f.lgd_gp_ward_code,
                    f.ulb,
                    f.lgd_district_code,
                    f.lgd_block_mc_code,
                    f.application_status,
                    f.next_level_role_id,
                    f.is_reverted,
                    f.total_family_members,
                    fm.id                                AS member_id,
                    fm.member_name,
                    fm.is_hof,
                    fm.mobile_no,
                    fm.aadhaar_no,
                    fm.date_of_birth,
                    fm.gender,
                    fm.social_category,
                    fm.digital_ration_card_no,
                    fm.applying_for_annapurna_bhandar
                FROM   dbt_apy.families f
                INNER  JOIN dbt_apy.family_members fm ON fm.family_id = f.id
                WHERE  {$memberWhere}
                ORDER  BY f.id ASC, fm.is_hof DESC, fm.id ASC
            ";

            $rows     = DB::connection('pgsql_ay')->select($membersSql, $memberBindings);
            
            // Resolve location names and status dynamically
            $this->resolveLocationNames($rows);

            $families = collect($rows)->groupBy('family_id');
        }

        // ── 5. Paginator for links ───────────────────────────────────────────
        $paginator = new LengthAwarePaginator(
            $families->values(),
            $total,
            $this->perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('livewire.annapurna-yojana.approver-index-table', [
            'families'  => $families,
            'paginator' => $paginator,
        ]);
    }

    /**
     * Bulk resolve LGD names from the default connection to avoid N+1 queries.
     */
    protected function resolveLocationNames(array $rows): void
    {
        if (empty($rows)) return;

        $districtIds = [];
        $blockIds = [];
        $gpIds = [];
        $ulbIds = [];
        $wardIds = [];

        foreach ($rows as $row) {
            if (!empty($row->lgd_district_code)) $districtIds[] = $row->lgd_district_code;
            if ($row->area_type === 'Rural') {
                if (!empty($row->lgd_block_mc_code)) $blockIds[] = $row->lgd_block_mc_code;
                if (!empty($row->lgd_gp_ward_code)) $gpIds[] = $row->lgd_gp_ward_code;
            } else {
                if (!empty($row->ulb)) $ulbIds[] = $row->ulb;
                if (!empty($row->lgd_gp_ward_code)) $wardIds[] = $row->lgd_gp_ward_code;
            }
        }

        $districts = !empty($districtIds) ? \App\Models\District::whereIn('id', array_unique($districtIds))->pluck('name', 'id') : collect();
        $blocks = !empty($blockIds) ? \App\Models\Block::whereIn('id', array_unique($blockIds))->pluck('name', 'id') : collect();
        $gps = !empty($gpIds) ? \App\Models\Panchayat::whereIn('id', array_unique($gpIds))->pluck('name', 'id') : collect();
        $ulbs = !empty($ulbIds) ? \App\Models\Municipality::whereIn('id', array_unique($ulbIds))->pluck('name', 'id') : collect();
        $wards = !empty($wardIds) ? \App\Models\Ward::whereIn('id', array_unique($wardIds))->pluck('name', 'id') : collect();

        foreach ($rows as $row) {
            $row->district = $districts[$row->lgd_district_code] ?? null;
            if ($row->area_type === 'Rural') {
                $row->block = $blocks[$row->lgd_block_mc_code] ?? null;
                $row->gp = $gps[$row->lgd_gp_ward_code] ?? null;
                $row->ward = null;
                $row->ulb = null;
            } else {
                $row->block = null;
                $row->gp = null;
                $row->ulb = $ulbs[$row->ulb] ?? null;
                $row->ward = $wards[$row->lgd_gp_ward_code] ?? null;
            }
            $row->status = $row->application_status ?? null;
        }
    }
}
