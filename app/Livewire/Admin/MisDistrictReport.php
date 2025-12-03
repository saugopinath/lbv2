<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\BeneficiaryPersonal;
use App\Models\District;
use Illuminate\Support\Collection;

class MisDistrictReport extends Component
{
    use WithPagination;

    public $perPage = 20;
    public $page = 1;

    // Role-levels
    private const ROLE_APPROVER = 144;
    private const ROLE_VERIFIER = 22;
    private const VERIFIED_REQUIRES_FINAL = true;

    protected $queryString = ['perPage', 'page'];

    /**
     * Main aggregation for MIS: Total, Approved, Verified (district-wise)
     */
    protected function getAggregatedByDistrict(): Collection
    {
        // Verified logic
        $verifiedExpr = self::VERIFIED_REQUIRES_FINAL
            ? 'SUM(CASE WHEN next_level_role_id = ? AND is_final_submit = true THEN 1 ELSE 0 END) as verified'
            : 'SUM(CASE WHEN next_level_role_id = ? THEN 1 ELSE 0 END) as verified';

        $selectRaw = "
            district_id,
            COUNT(*) as total,
            SUM(CASE WHEN next_level_role_id = ? THEN 1 ELSE 0 END) as approved,
            {$verifiedExpr}
        ";

        $bindings = [
            self::ROLE_APPROVER,   // for approved
            self::ROLE_VERIFIER    // for verified
        ];

        $rows = BeneficiaryPersonal::query()
            ->selectRaw($selectRaw, $bindings)
            ->groupBy('district_id')
            ->get();

        // Load district names
        $districtIds = $rows->pluck('district_id')->toArray();
        $districtNames = District::whereIn('id', $districtIds)->pluck('name', 'id')->toArray();

        // Map results
        return $rows->map(function ($r) use ($districtNames) {
            return (object) [
                'district_id'      => $r->district_id,
                'district_display' => $districtNames[$r->district_id] ?? 'Unknown District',
                'total'            => (int) $r->total,
                'approved'         => (int) $r->approved,
                'verified'         => (int) $r->verified,
            ];
        });
    }

    /**
     * CSV Export
     */
    public function exportCsv()
    {
        $rows = $this->getAggregatedByDistrict();

        $filename = 'mis_districts_' . now()->format('Ymd_His') . '.csv';

        $callback = function() use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['district_id', 'district', 'total', 'approved', 'verified']);

            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->district_id,
                    $r->district_display,
                    $r->total,
                    $r->approved,
                    $r->verified
                ]);
            }
            fclose($out);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function render()
    {
        $all = $this->getAggregatedByDistrict();

        // Manual Pagination
        $page = max(1, $this->page);
        $perPage = max(1, $this->perPage);
        $offset = ($page - 1) * $perPage;

        $paged = $all->slice($offset, $perPage)->values();

        $totals = [
            'total'    => $all->sum('total'),
            'approved' => $all->sum('approved'),
            'verified' => $all->sum('verified'),
        ];

        return view('livewire.admin.mis-district-report', [
            'rows' => $paged,
            'totals' => $totals,
            'totalRows' => $all->count(),
            'page' => $page,
            'perPage' => $perPage,
        ]);
    }
}
