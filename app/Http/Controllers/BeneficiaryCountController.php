<?php

namespace App\Http\Controllers;

use App\Helpers\LgdFilterHelper;
use App\Models\BeneficiaryCommonList;
use App\Models\BeneficiaryPersonal;
use App\Models\BenRejectDetails;
use App\Models\Codemaster;
use App\Models\District;
use App\Models\DraftBeneficiaryPersonal;
use Database\Seeders\BenRejectDetailsSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Exports\ArrayExport;
use Maatwebsite\Excel\Facades\Excel;

class BeneficiaryCountController extends Controller
{

    // public function index(Request $request)
    // {
    //     $header = 'Beneficiary Wise report List';
    //     $columns = [
    //         ['label' => 'District', 'field' => 'district_display', 'sortable' => false],
    //         ['label' => 'Total', 'field' => 'total', 'sortable' => false],
    //         ['label' => 'Approved', 'field' => 'approved', 'sortable' => false],
    //         ['label' => 'Verified', 'field' => 'verified', 'sortable' => false],
    //         ['label' => 'Reverted', 'field' => 'reverted', 'sortable' => false],
    //         ['label' => 'Reject', 'field' => 'rejected', 'sortable' => false],
    //     ];

    //     // $page = max(1, (int) $request->input('page', 1));
    //     // $perPage = max(1, (int) $request->input('perPage', 20));
    //     $Verified  = Codemaster::getIdByCode(23);
    //     $Approved  = Codemaster::getIdByCode(0);
    //     $reverted = Codemaster::getIdByCode(21);
    //     $sourceableClass = BeneficiaryPersonal::class;
    //     $next_level_role_id = $Approved;
    //     $next_level_role_id2 = $Verified;
    //     $next_level_role_id3 = $reverted;
    //     $sourceableClass2 = DraftBeneficiaryPersonal::class;
    //     $sourceableClass3 = BenRejectDetails::class;
    //     // ---------- 1) TOTAL (from beneficiary_personals) ----------
    //     // Only select minimal fields
    //     $personals = BeneficiaryCommonList::select('sourceable_id', 'beneficiary_id', 'district_id')->get();
    //     $totalByDistrict = $personals
    //         ->groupBy('district_id')
    //         ->map(fn($g) => $g->count());

    //     $approvedCommon = BeneficiaryCommonList::whereHasMorph(
    //         'sourceable',
    //         $sourceableClass,
    //         function ($q) use ($next_level_role_id) {
    //             $q->where('next_level_role_id', $next_level_role_id);
    //         }
    //     )
    //         ->with(['sourceable:application_id,district_id'])
    //         ->get();
    //     // dd($approvedCommon->toSql(), $approvedCommon->getBindings());
    //     $approvedByDistrict = $approvedCommon
    //         ->filter(fn($r) => $r->sourceable && $r->sourceable->district_id)
    //         ->groupBy(fn($r) => $r->sourceable->district_id)
    //         ->map(fn($g) => $g->pluck('sourceable_id')->unique()->count());
    //     // dd($approvedByDistrict);
    //     // ---------- 3) VERIFIED (from beneficiary_common_lists -> sourceable = DraftBeneficiaryPersonal) ----------
    //     // $verifiedByDistrict = collect(); // default empty
    //     // if (class_exists(DraftBeneficiaryPersonal::class)) {
    //     $verifiedCommon = BeneficiaryCommonList::whereHasMorph(
    //         'sourceable',
    //         $sourceableClass2,
    //         function ($q) use ($next_level_role_id2) {
    //             $q->where('next_level_role_id', $next_level_role_id2);
    //         }
    //     )
    //         ->with(['sourceable:application_id,district_id'])
    //         ->get();
    //     $verifiedByDistrict = $verifiedCommon
    //         ->filter(fn($r) => $r->sourceable && $r->sourceable->district_id)
    //         ->groupBy(fn($r) => $r->sourceable->district_id)
    //         ->map(fn($g) => $g->pluck('sourceable_id')->unique()->count());
    //     // }
    //     $revertedCommon = BeneficiaryCommonList::whereHasMorph(
    //         'sourceable',
    //         $sourceableClass2,
    //         function ($q) use ($next_level_role_id3) {
    //             $q->where('next_level_role_id', $next_level_role_id3)
    //                 ->where('is_final_submit', true);
    //         }
    //     )
    //         ->with(['sourceable:application_id,district_id'])
    //         ->get();
    //     $revertedByDistrict = $revertedCommon
    //         ->filter(fn($r) => $r->sourceable && $r->sourceable->district_id)
    //         ->groupBy(fn($r) => $r->sourceable->district_id)
    //         ->map(fn($g) => $g->pluck('sourceable_id')->unique()->count());

    //     // dd($revertedByDistrict);
    //     // ---------- 4) REJECT (if you use is_faulty on personal) ----------
    //     $rejectCommon = BeneficiaryCommonList::whereHasMorph(
    //         'sourceable',
    //         $sourceableClass3
    //     )->where('is_reject', true)
    //         ->with(['sourceable:application_id,district_id'])
    //         ->get();
    //     $rejectedByDistrict = $rejectCommon
    //         ->filter(fn($r) => $r->sourceable && $r->sourceable->district_id)
    //         ->groupBy(fn($r) => $r->sourceable->district_id)
    //         ->map(function ($group) {
    //             $applicationIds = $group->pluck('sourceable.application_id')->unique();

    //             return BenRejectDetails::whereIn('application_id', $applicationIds)->count();
    //         });

    //     $districtIds = District::pluck('id')->values()->all();
    //     $districtNames = District::pluck('name', 'id')->toArray();
    //     $rows = collect($districtIds)->map(function ($did) use ($districtNames, $totalByDistrict, $approvedByDistrict, $verifiedByDistrict, $revertedByDistrict, $rejectedByDistrict) {
    //         return (object)[
    //             'district_id' => $did,
    //             'district_display' => $districtNames[$did] ?? ('District ' . ($did ?? '-')),
    //             'total' => (int) ($totalByDistrict->get($did, 0)),
    //             'approved' => (int) ($approvedByDistrict->get($did, 0)),
    //             'verified' => (int) ($verifiedByDistrict->get($did, 0)),
    //             'reverted' => (int) ($revertedByDistrict->get($did, 0)),
    //             'rejected' => (int) ($rejectedByDistrict->get($did, 0)),
    //         ];
    //     });
    //     $totalRows = $rows->count();
    //     $paged = $rows->values();
    //     // dd($paged);
    //     $totals = [
    //         'total'    => $rows->sum('total'),
    //         'approved' => $rows->sum('approved'),
    //         'verified' => $rows->sum('verified'),
    //         'reverted' => $rows->sum('reverted'),
    //         'rejected'   => $rows->sum('rejected'),
    //     ];

    //     return view('BeneficiaryCount.beneficiary_count', [
    //         'header'    => $header,
    //         'columns'   => $columns,
    //         'rows'      => $paged,
    //         'totalRows' => $totalRows,
    //         'totals'    => $totals,
    //     ]);
    // }
    // public function misReport(Request $request)
    // {
    //     $header = 'Beneficiary Count  wise Mis Report';
    //     return view('BeneficiaryCount.beneficiary_count_report', [
    //         'header'    => $header,
    //     ]);
    // }

    public function ApplicationMisReport(Request $request)
    {
        // Log::debug('=== ApplicationMisReport START ===');

        $massage = 'Wise Beneficiary Mis Report';

        $helperData = LgdFilterHelper::getCodesAndInitialCounts($request);
        // dd($helperData);
        // Log::debug('Helper data received', $helperData);

        $masterLocations = $helperData['master_locations'] ?? [];
        $mode = $helperData['mode'] ?? null;
        $col = $helperData['col'] ?? null;
        $name = $helperData['name'] ?? null;
        $blockIds = $helperData['block_ids'] ?? [];
        $subdivisionIds = $helperData['sub_division_ids'] ?? [];

        // Log::debug('Master locations', ['count' => count($masterLocations), 'mode' => $mode, 'col' => $col]);

        // Role IDs
        $pendingRoleId = Codemaster::getIdByCode(22);
        $verifiedRoleId = Codemaster::getIdByCode(23);
        $approvedRoleId = Codemaster::getIdByCode(0);
        $rejectedRoleId = Codemaster::getIdByCode(-1);
        $revertRoleId = Codemaster::getIdByCode( 21);

        // Log::debug('Role IDs', [
        //     'pending' => $pendingRoleId,
        //     'verified' => $verifiedRoleId,
        //     'approved' => $approvedRoleId,
        //     'rejected' => $rejectedRoleId,
        //     'reverted' => $revertRoleId,
        // ]);

        // Build base filters
        $baseFilters = [];
        if (!empty($helperData['district_code'])) {
            $baseFilters['district_id'] = $helperData['district_code'];
        }
        if (!empty($helperData['block_code'])) {
            $baseFilters['block_id'] = $helperData['block_code'];
        }
        if (!empty($helperData['subdivission_code'])) {
            $baseFilters['sub_division_id'] = $helperData['subdivission_code'];
        }
        if (!empty($helperData['rural_urban_code'])) {
            $baseFilters['cd_rural_urban_id'] = $helperData['rural_urban_code'];
        }
        if (!empty($helperData['gpWard_code'])) {
            $baseFilters['cd_gp_ward_id'] = $helperData['gpWard_code'];
        }

        // Log::debug('Base filters applied', $baseFilters);

        // Initialize location counts
        $locationCounts = [];
        $locationNames = [];
        $columns = $this->getColumnsByMode($mode);
        // $columns = [
        //     ['key' => 'location_name', 'label' => 'Location', 'align' => 'left', 'type' => 'text'],
        //     ['key' => 'pending',       'label' => 'Pending verification', 'align' => 'right', 'type' => 'number', 'show_total' => true],
        //     ['key' => 'verified',      'label' => 'Verified', 'align' => 'right', 'type' => 'number', 'show_total' => true],
        //     ['key' => 'approved',      'label' => 'Approved', 'align' => 'right', 'type' => 'number', 'show_total' => true],
        //     ['key' => 'rejected',      'label' => 'Rejected', 'align' => 'right', 'type' => 'number', 'show_total' => true],
        //     ['key' => 'reverted',      'label' => 'Reverted', 'align' => 'right', 'type' => 'number', 'show_total' => true],
        //     ['key' => 'total',         'label' => 'Total', 'align' => 'right', 'type' => 'number', 'show_total' => true],
        // ];

        foreach ($masterLocations as $loc) {
            $key = $loc['location_id'];
            $locationNames[$key] = $loc['location_name'];
            $locationCounts[$key] = [
                'location_name' => $loc['location_name'],
                'pending' => 0,
                'verified' => 0,
                'approved' => 0,
                'rejected' => 0,
                'reverted' => 0,
            ];
        }

        // Log::debug('Location counts initialized', ['count' => count($locationCounts)]);

        if (empty($masterLocations)) {

            // Log::warning('No master locations found');
            return view('BeneficiaryCount.beneficiary_count_report', [
                'header'  => $massage,
                'helper'  => $helperData,
                'columns' => $columns,
                'name' => $name,
                'data'    => []
            ]);
        }

        // Build base query
        $baseQuery = $this->buildBaseQuery($baseFilters);
        // Log::debug('Base query built');

        if ($mode === 'block_subdivision') {
            // Log::debug('=== Processing BLOCK_SUBDIVISION mode ===');

            // Extract block/subdivision IDs
            if (empty($blockIds) && empty($subdivisionIds)) {
                foreach ($masterLocations as $loc) {
                    $k = $loc['location_id'];
                    if (is_string($k) && str_contains($k, '_')) {
                        [$pref, $id] = explode('_', $k, 2);
                        if ($pref === 'block') $blockIds[] = (int)$id;
                        if ($pref === 'sub') $subdivisionIds[] = (int)$id;
                    }
                }
            }

            // Log::debug('Extracted IDs', ['blockIds' => $blockIds, 'subdivisionIds' => $subdivisionIds]);

            $anyBlocks = !empty($blockIds);
            $anySubdivs = !empty($subdivisionIds);

            if (!$anyBlocks && !$anySubdivs) {
                // Log::warning('No block or subdivision IDs found');
                return view('BeneficiaryCount.beneficiary_count_report', [
                    'header'  => $massage,
                    'helper'  => $helperData,
                    'columns' => $columns,
                    'name' => $name,
                    'data'    => []
                ]);
            }

            // For block_subdivision mode, count each status separately for blocks
            foreach ($blockIds as $blockId) {
                $key = 'block_' . $blockId;
                // Log::debug("Processing block {$blockId}");

                if (!isset($locationCounts[$key])) {
                    $locationCounts[$key] = [
                        'location_name' => $locationNames[$key] ?? "Block {$blockId}",
                        'pending' => 0,
                        'verified' => 0,
                        'approved' => 0,
                        'rejected' => 0,
                        'reverted' => 0,
                    ];
                }

                $query = (clone $baseQuery)->where('block_id', $blockId);
                $total = $query->count();
                // Log::debug("Block {$blockId} total records", ['count' => $total]);

                $locationCounts[$key]['pending'] = $this->countByRoleId((clone $query), $pendingRoleId);
                $locationCounts[$key]['verified'] = $this->countByRoleId((clone $query), $verifiedRoleId);
                $locationCounts[$key]['approved'] = $this->countByRoleId((clone $query), $approvedRoleId);
                $locationCounts[$key]['rejected'] = $this->countByRoleIdwithflag((clone $query), $rejectedRoleId);
                $locationCounts[$key]['reverted'] = $this->countByRoleIdreverted((clone $query), $revertRoleId);

                // Log::debug("Block {$blockId} status counts", $locationCounts[$key]);
            }

            // Process subdivisions
            foreach ($subdivisionIds as $subId) {
                $key = 'sub_' . $subId;
                // Log::debug("Processing subdivision {$subId}");

                if (!isset($locationCounts[$key])) {
                    $locationCounts[$key] = [
                        'location_name' => $locationNames[$key] ?? "Subdivision {$subId}",
                        'pending' => 0,
                        'verified' => 0,
                        'approved' => 0,
                        'rejected' => 0,
                        'reverted' => 0,
                    ];
                }

                $query = (clone $baseQuery)->where('sub_division_id', $subId);
                $total = $query->count();
                // Log::debug("Subdivision {$subId} total records", ['count' => $total]);

                $locationCounts[$key]['pending'] = $this->countByRoleId((clone $query), $pendingRoleId);
                $locationCounts[$key]['verified'] = $this->countByRoleId((clone $query), $verifiedRoleId);
                $locationCounts[$key]['approved'] = $this->countByRoleId((clone $query), $approvedRoleId);
                $locationCounts[$key]['rejected'] = $this->countByRoleIdwithflag((clone $query), $rejectedRoleId);
                $locationCounts[$key]['reverted'] = $this->countByRoleIdReverted((clone $query), $revertRoleId);

                // Log::debug("Subdivision {$subId} status counts", $locationCounts[$key]);
            }
        } else {
            // Log::debug('=== Processing NORMAL mode ===');

            // Normal modes
            if (empty($col)) {
                $col = 'district_id';
            }
            $ids = [];
            foreach ($masterLocations as $loc) {
                if (is_numeric($loc['location_id'])) {
                    $ids[] = (int)$loc['location_id'];
                }
            }
            // Log::debug('Location IDs for normal mode', ['ids' => $ids, 'column' => $col]);
            if (empty($ids)) {
                // Log::warning('No numeric location IDs found');
                return view('BeneficiaryCount.beneficiary_count_report', [
                    'header'  => $massage,
                    'helper'  => $helperData,
                    'columns' => $columns,
                    'name' => $name,
                    'data'    => []
                ]);
            }

            // Count each status for each location ID
            foreach ($ids as $locId) {
                $locKey = (string)$locId;
                if (!isset($locationCounts[$locKey]) && isset($locationCounts[(int)$locId])) {
                    $locKey = (int)$locId;
                }

                if (!isset($locationCounts[$locKey])) {
                    $locationCounts[$locKey] = [
                        'location_name' => $locationNames[$locKey] ?? $locKey,
                        'pending' => 0,
                        'verified' => 0,
                        'approved' => 0,
                        'rejected' => 0,
                        'reverted' => 0,
                    ];
                }

                $query = (clone $baseQuery)->where($col, $locId);
                $total = $query->count();
                // Log::debug("Location {$locId} ({$col}) total records", ['count' => $total]);

                $locationCounts[$locKey]['pending'] = $this->countByRoleId((clone $query), $pendingRoleId);
                $locationCounts[$locKey]['verified'] = $this->countByRoleId((clone $query), $verifiedRoleId);
                $locationCounts[$locKey]['approved'] = $this->countByRoleId((clone $query), $approvedRoleId);
                $locationCounts[$locKey]['rejected'] = $this->countByRoleIdwithflag((clone $query), $rejectedRoleId);
                $locationCounts[$locKey]['reverted'] = $this->countByRoleIdReverted((clone $query), $revertRoleId);

                // Log::debug("Location {$locId} status counts", $locationCounts[$locKey]);
            }
        }

        // Ensure integers
        foreach ($locationCounts as &$counts) {
            $counts['pending'] = (int)($counts['pending'] ?? 0);
            $counts['verified'] = (int)($counts['verified'] ?? 0);
            $counts['approved'] = (int)($counts['approved'] ?? 0);
            $counts['rejected'] = (int)($counts['rejected'] ?? 0);
            $counts['reverted'] = (int)($counts['reverted'] ?? 0);
        }

        // Log::debug('=== FINAL LOCATION COUNTS ===', $locationCounts);
        // Log::debug('=== ApplicationMisReport END ===');
        // $columns = [
        //     ['key' => 'location_name', 'label' => 'Location', 'align' => 'left',  'type' => 'text'],
        //     ['key' => 'pending',       'label' => 'Pending verification', 'align' => 'right', 'type' => 'number', 'show_total' => true],
        //     ['key' => 'verified',      'label' => 'Verified',             'align' => 'right', 'type' => 'number', 'show_total' => true],
        //     ['key' => 'approved',      'label' => 'Approved',             'align' => 'right', 'type' => 'number', 'show_total' => true],
        //     ['key' => 'rejected',      'label' => 'Rejected',             'align' => 'right', 'type' => 'number', 'show_total' => true],
        //     ['key' => 'reverted',      'label' => 'Reverted',             'align' => 'right', 'type' => 'number', 'show_total' => true],
        //     ['key' => 'total',         'label' => 'Total',                'align' => 'right', 'type' => 'number', 'show_total' => true],
        // ];

        $data = [];
        foreach ($locationCounts as $key => $row) {
            $pending  = (int)($row['pending'] ?? 0);
            $verified = (int)($row['verified'] ?? 0);
            $approved = (int)($row['approved'] ?? 0);
            $rejected = (int)($row['rejected'] ?? 0);
            $reverted = (int)($row['reverted'] ?? 0);
            $total = $pending + $verified + $approved + $rejected + $reverted;

            $data[] = [
                'location_name' => $row['location_name'] ?? $key,
                'pending' => $pending,
                'verified' => $verified,
                'approved' => $approved,
                'rejected' => $rejected,
                'reverted' => $reverted,
                'total' => $total,
            ];
        }

        
        return view('BeneficiaryCount.beneficiary_count_report', [
            // 'header' => $header,
            // 'helper' => $helperData,
            // 'locationCounts' => $locationCounts,
            'header' => $massage,
            'helper' => $helperData,
            'columns' => $columns,
            'data' => $data,
            'name' => $name,
            'exportUrl' => route('reports-export'),
            'filename' => 'application-mis-report.xlsx',
        ]);
    }

    private function getColumnsByMode(?string $mode,): array
    {
        // Default location label
        $locationLabel = match ($mode) {
            'block_subdivision' => 'Block / Subdivision',
            'district' => 'District',
            'block' => 'Block',
            'subdivision' => 'Subdivision',
            'gp_ward' => 'GP / Ward',
            'municipality' => 'Municipality',
            'ward' => 'Ward',
            default => 'Location'
        };

        return [
            ['key' => 'location_name', 'label' => $locationLabel, 'align' => 'left', 'type' => 'text'],
            ['key' => 'pending', 'label' => 'Pending verification', 'align' => 'right', 'type' => 'number', 'show_total' => true],
            ['key' => 'verified', 'label' => 'Verified', 'align' => 'right', 'type' => 'number', 'show_total' => true],
            ['key' => 'approved', 'label' => 'Approved', 'align' => 'right', 'type' => 'number', 'show_total' => true],
            ['key' => 'rejected', 'label' => 'Rejected', 'align' => 'right', 'type' => 'number', 'show_total' => true],
            ['key' => 'reverted', 'label' => 'Reverted', 'align' => 'right', 'type' => 'number', 'show_total' => true],
            ['key' => 'total', 'label' => 'Total', 'align' => 'right', 'type' => 'number', 'show_total' => true],
        ];
    }
    private function buildBaseQuery(array $baseFilters)
    {
        $query = BeneficiaryCommonList::query();

        foreach ($baseFilters as $column => $value) {
            $query->where($column, $value);
        }

        return $query;
    }

    /**
     * Count records by role ID using ORM count()
     */
    private function countByRoleId($query, int $roleId): int
    {
        $count = (clone $query)
            ->where('next_level_role_id', $roleId)
            ->count();

        // Log::debug("Counting role {$roleId}", ['result' => $count]);

        return $count;
    }
    private function countByRoleIdwithflag($query, int $roleId): int
    {
        $count = (clone $query)
            ->where('next_level_role_id', $roleId)
            ->where('is_reject', true)
            ->count();

        // Log::debug("Counting role {$roleId}", ['result' => $count]);

        return $count;
    }
    private function countByRoleIdReverted($query, int $roleId): int
    {
        $count = (clone $query)
            ->where('next_level_role_id', $roleId)
            ->whereHasMorph(
                'sourceable',
                DraftBeneficiaryPersonal::class,
                function ($q) {
                    $q->where('is_final_submit', true);
                }
            )
            ->count();

        // Log::debug("Counting reverted role {$roleId} (requires sourceable.is_final = true)", ['result' => $count]);

        return $count;
    }

    public function exportExcel(Request $request)
    {
        try {
            // Decode incoming base64 JSON
            $columns = json_decode(base64_decode($request->input('columns', '')), true) ?? [];
            $rows    = json_decode(base64_decode($request->input('data', '')), true) ?? [];

            // Build Header Row
            $headerRow = array_map(fn($c) => $c['label'], $columns);

            // Build Table Rows
            $dataRows = [];
            foreach ($rows as $row) {
                $temp = [];
                foreach ($columns as $col) {
                    $key = $col['key'];
                    $temp[] = $row[$key] ?? '';
                }
                $dataRows[] = $temp;
            }

            // Merge Header + Rows
            $exportArray = array_merge([$headerRow], $dataRows);

            $fileName = $request->input('filename', 'mis-report.xlsx');

            return Excel::download(new ArrayExport($exportArray), $fileName);
        } catch (\Exception $e) {

            return back()->with('error', 'Failed to export Excel. Please try again.');
        }
    }
}
