<?php

namespace App\Livewire\AnnapurnaYojana;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class VerifierIndexTable extends Component
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
     * Build shared WHERE clause and bindings.
     *
     * LGD → dbt_apy.families column mapping:
     *   district_id  → f.district
     *   rural_urban=1 (Urban)  → area_type='Urban', blockurban → f.ulb,  gp_ward → f.ward
     *   rural_urban=2 (Rural)  → area_type='Rural', blockurban → f.block, gp_ward → f.gp
     */
    private function buildWhere(): array
    {
        $conditions = ['1=1'];
        $bindings   = [];

        // District
        if ($this->district_id) {
            $conditions[] = 'f.district = ?';
            $bindings[]   = $this->district_id;
        }

        // Area type + block/ulb + gp/ward
        if ($this->rural_urban == 1) {
            // Urban
            $conditions[] = "f.area_type ILIKE 'Urban'";
            if ($this->blockurban) {
                $conditions[] = 'f.ulb = ?';
                $bindings[]   = $this->blockurban;
            }
            if ($this->gp_ward) {
                $conditions[] = 'f.ward = ?';
                $bindings[]   = $this->gp_ward;
            }
        } elseif ($this->rural_urban == 2) {
            // Rural
            $conditions[] = "f.area_type ILIKE 'Rural'";
            if ($this->blockurban) {
                $conditions[] = 'f.block = ?';
                $bindings[]   = $this->blockurban;
            }
            if ($this->gp_ward) {
                $conditions[] = 'f.gp = ?';
                $bindings[]   = $this->gp_ward;
            }
        }

        // Gender
        if ($this->gender !== '') {
            $conditions[] = 'fm.gender ILIKE ?';
            $bindings[]   = $this->gender;
        }

        // Free-text search
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
                    f.gp,
                    f.ulb,
                    f.ward,
                    f.district,
                    f.block,
                    f.status,
                    f.total_family_members,
                    fm.id                                AS member_id,
                    fm.member_name,
                    fm.is_ho_f                           AS is_hof,
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
                ORDER  BY f.id ASC, fm.is_ho_f DESC, fm.id ASC
            ";

            $rows     = DB::connection('pgsql_ay')->select($membersSql, $memberBindings);
            $families = collect($rows)->groupBy('family_id');
        }

        // ── 4. Stats counters ────────────────────────────────────────────────
        $statsSql = "
            SELECT
                COUNT(DISTINCT f.id)                                               AS total_families,
                SUM(f.total_family_members)                                        AS total_members,
                COUNT(DISTINCT CASE WHEN f.status ILIKE 'Pending'  THEN f.id END) AS pending,
                COUNT(DISTINCT CASE WHEN f.status ILIKE 'Verified' THEN f.id END) AS verified,
                COUNT(DISTINCT CASE WHEN f.status ILIKE 'Approved' THEN f.id END) AS approved
            FROM dbt_apy.families f
            INNER JOIN dbt_apy.family_members fm ON fm.family_id = f.id
            WHERE {$where}
        ";
        $stats = DB::connection('pgsql_ay')->selectOne($statsSql, $bindings);

        // ── 5. Paginator for links ───────────────────────────────────────────
        $paginator = new LengthAwarePaginator(
            $families->values(),
            $total,
            $this->perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('livewire.annapurna-yojana.verifier-index-table', [
            'families'  => $families,
            'paginator' => $paginator,
            'stats'     => $stats,
        ]);
    }
}
