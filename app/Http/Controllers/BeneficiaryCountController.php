<?php

namespace App\Http\Controllers;

use App\Models\BeneficiaryCommonList;
use App\Models\BeneficiaryPersonal;
use App\Models\BenRejectDetails;
use App\Models\Codemaster;
use App\Models\District;
use App\Models\DraftBeneficiaryPersonal;
use Database\Seeders\BenRejectDetailsSeeder;
use Illuminate\Http\Request;

class BeneficiaryCountController extends Controller
{

    public function index(Request $request)
    {
        $header = 'Beneficiary Wise report List';
        $columns = [
            ['label' => 'District', 'field' => 'district_display', 'sortable' => false],
            ['label' => 'Total', 'field' => 'total', 'sortable' => false],
            ['label' => 'Approved', 'field' => 'approved', 'sortable' => false],
            ['label' => 'Verified', 'field' => 'verified', 'sortable' => false],
            ['label' => 'Reverted', 'field' => 'reverted', 'sortable' => false],
            ['label' => 'Reject', 'field' => 'rejected', 'sortable' => false],
        ];

        // $page = max(1, (int) $request->input('page', 1));
        // $perPage = max(1, (int) $request->input('perPage', 20));
        $Verified  = Codemaster::getIdByCode(23);
        $Approved  = Codemaster::getIdByCode(0);
        $reverted = Codemaster::getIdByCode(21);
        $sourceableClass = BeneficiaryPersonal::class;
        $next_level_role_id = $Approved;
        $next_level_role_id2 = $Verified;
        $next_level_role_id3 = $reverted;
        $sourceableClass2 = DraftBeneficiaryPersonal::class;
        $sourceableClass3 = BenRejectDetails::class;
        // ---------- 1) TOTAL (from beneficiary_personals) ----------
        // Only select minimal fields
        $personals = BeneficiaryCommonList::select('sourceable_id', 'beneficiary_id', 'district_id')->get();
        $totalByDistrict = $personals
            ->groupBy('district_id')
            ->map(fn($g) => $g->count());
            
        $approvedCommon = BeneficiaryCommonList::whereHasMorph(
            'sourceable',
            $sourceableClass,
            function ($q) use ($next_level_role_id) {
                $q->where('next_level_role_id', $next_level_role_id);
            }
        )
            ->with(['sourceable:application_id,district_id'])
            ->get();
        // dd($approvedCommon->toSql(), $approvedCommon->getBindings());
        $approvedByDistrict = $approvedCommon
            ->filter(fn($r) => $r->sourceable && $r->sourceable->district_id)
            ->groupBy(fn($r) => $r->sourceable->district_id)
            ->map(fn($g) => $g->pluck('sourceable_id')->unique()->count());
            // dd($approvedByDistrict);
        // ---------- 3) VERIFIED (from beneficiary_common_lists -> sourceable = DraftBeneficiaryPersonal) ----------
        // $verifiedByDistrict = collect(); // default empty
        // if (class_exists(DraftBeneficiaryPersonal::class)) {
            $verifiedCommon = BeneficiaryCommonList::whereHasMorph(
                'sourceable',
                $sourceableClass2,
                function ($q) use ($next_level_role_id2) {
                    $q->where('next_level_role_id', $next_level_role_id2);
                }
            )
                ->with(['sourceable:application_id,district_id'])
                ->get();
        $verifiedByDistrict = $verifiedCommon
                ->filter(fn($r) => $r->sourceable && $r->sourceable->district_id)
                ->groupBy(fn($r) => $r->sourceable->district_id)
                ->map(fn($g) => $g->pluck('sourceable_id')->unique()->count());
        // }
        $revertedCommon = BeneficiaryCommonList::whereHasMorph(
            'sourceable',
            $sourceableClass2,
            function ($q) use ($next_level_role_id3) {
                $q->where('next_level_role_id', $next_level_role_id3)
                    ->where('is_final_submit', true);
            }
        )
            ->with(['sourceable:application_id,district_id'])
            ->get();
        $revertedByDistrict = $revertedCommon
            ->filter(fn($r) => $r->sourceable && $r->sourceable->district_id)
            ->groupBy(fn($r) => $r->sourceable->district_id)
            ->map(fn($g) => $g->pluck('sourceable_id')->unique()->count());

            // dd($revertedByDistrict);
        // ---------- 4) REJECT (if you use is_faulty on personal) ----------
         $rejectCommon = BeneficiaryCommonList::whereHasMorph( 'sourceable',
            $sourceableClass3)->where('is_reject',true)
            ->with(['sourceable:application_id,district_id'])
            ->get();
        $rejectedByDistrict = $rejectCommon
            ->filter(fn($r) => $r->sourceable && $r->sourceable->district_id)
            ->groupBy(fn($r) => $r->sourceable->district_id)
            ->map(function ($group) {
                $applicationIds = $group->pluck('sourceable.application_id')->unique();

                return BenRejectDetails::whereIn('application_id', $applicationIds)->count();
            });

        $districtIds = District::pluck('id')->values()->all();
        $districtNames = District::pluck('name', 'id')->toArray();
        $rows = collect($districtIds)->map(function ($did) use ($districtNames, $totalByDistrict, $approvedByDistrict, $verifiedByDistrict, $revertedByDistrict, $rejectedByDistrict) {
            return (object)[
                'district_id' => $did,
                'district_display' => $districtNames[$did] ?? ('District ' . ($did ?? '-')),
                'total' => (int) ($totalByDistrict->get($did, 0)),
                'approved' => (int) ($approvedByDistrict->get($did, 0)),
                'verified' => (int) ($verifiedByDistrict->get($did, 0)),
                'reverted' => (int) ($revertedByDistrict->get($did, 0)),
                'rejected' => (int) ($rejectedByDistrict->get($did, 0)),
            ];
        });
        $totalRows = $rows->count();
        $paged = $rows->values();
        // dd($paged);
        $totals = [
            'total'    => $rows->sum('total'),
            'approved' => $rows->sum('approved'),
            'verified' => $rows->sum('verified'),
            'reverted' => $rows->sum('reverted'),
            'rejected'   => $rows->sum('rejected'),
        ];

        return view('BeneficiaryCount.beneficiary_count', [
            'header'    => $header,
            'columns'   => $columns,
            'rows'      => $paged,
            'totalRows' => $totalRows,
            'totals'    => $totals,
        ]);
    }
    public function misReport(Request $request)
    {
        $header='Beneficiary Count  wise Mis Report';
         return view('BeneficiaryCount.beneficiary_count_report', [
            'header'    => $header, 
        ]);
    }
}
