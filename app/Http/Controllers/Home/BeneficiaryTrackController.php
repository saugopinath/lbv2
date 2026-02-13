<?php

namespace App\Http\Controllers;

use App\Models\BenDocs;
use App\Models\Beneficiary;
use App\Models\BeneficiaryJBLB;
use App\Models\BenEntry;
use App\Models\District;
use App\Models\GP;
use App\Models\Scheme;
use App\Models\Taluka;
use App\Models\UrbanBody;
use App\Models\Ward;
use App\Models\DocumentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BeneficiaryTrackController extends Controller
{
    public function trackBeneficiary(Request $request)
    {


        // Initial Page Load
        $schemes = Scheme::where('is_active', 1)->get();
        $districts = District::all();

        // Count total accessible records via Scout
        $results = Beneficiary::search('*')->get()->count();

        // Fetch Location Data for Scripts
        $blocks = DB::table('m_block')
            ->select('block_code as id', 'block_name as text', 'district_code')
            ->get();

        $subDistricts = DB::table('m_sub_district')
            ->select('sub_district_code as id', 'sub_district_name as text', 'district_code')
            ->get();

        $ulbs = DB::table('m_urban_body')
            ->select('urban_body_code as id', 'urban_body_name as text', 'district_code', 'sub_district_code')
            ->get();

        $gps = DB::table('m_gp')
            ->select('gram_panchyat_code as id', 'gram_panchyat_name as text', 'district_code', 'block_code')
            ->get();

        $ulb_wards = DB::table('m_urban_body_ward')
            ->select('urban_body_ward_code as id', 'urban_body_ward_name as text', 'urban_body_code')
            ->get();

        return view('track-ben.ben-track', compact(
            'schemes',
            'districts',
            'results',
            'blocks',
            'subDistricts',
            'ulbs',
            'gps',
            'ulb_wards'
        ));
    }

    public function trackBeneficiaryData(Request $request)
    {
        try {

            if ($request->wantsJson()) {

                $limit = 100;
                $offset = (int) $request->get('offset', 0);
                $page = ($offset / $limit) + 1;

                $search = $request->search;

                // 🔥 Meilisearch query
                $scout = BeneficiaryJBLB::search($search ?: '');

                // ✅ Filters (must match filterableAttributes exactly)

                if ($request->scheme) {
                    $scout->where('scheme_id', (int) $request->scheme);
                }

                if ($request->district) {
                    $scout->where('district_id', (int) $request->district);
                }

                if ($request->urban_code) {
                    $scout->where('rural_urban', $request->urban_code);
                }

                if ($request->block) {
                    $scout->where('blockurban', (int) $request->block);
                }

                if ($request->muncid) {
                    $scout->where('blockurban', (int) $request->muncid);
                }

                if ($request->gp_ward) {
                    $scout->where('gpward', (int) $request->gp_ward);
                }

                // ✅ Sort by application_id (must be sortable attribute)
                $beneficiaries = $scout
                    ->orderBy('application_id', 'desc')
                    ->paginate($limit, 'page', $page);

                $total = $beneficiaries->total();
                // dd($total);
                $html = '';

                foreach ($beneficiaries as $b) {

                    // 🔥 No more DB queries if you include names in index
                    $districtName = $b->district_name ?? 'Unknown';
                    $schemeName = $b->scheme_name ?? 'Unknown';

                    $status = $b->next_level_role_id == 0 ? 'Approved' : 'Approval Pending';
                    $statusClass = $b->next_level_role_id == 0
                        ? 'status-active'
                        : 'status-pending';

                    $html .= view('track-ben.beneficiary-card', [
                        'status' => $status,
                        'statusClass' => $statusClass,
                        'beneficiaryId' => $b->application_id,
                        'name' => $b->beneficiary_name ?? 'N/A',
                        'relation' => 'N/A',
                        'relationName' => 'N/A',
                        'schemeName' => $schemeName,
                        'location' => $districtName . ', West Bengal',
                        'mobile' => $b->mobile ?? 'N/A'
                    ])->render();
                }

                return response()->json([
                    'html' => $html,
                    'total' => $total,
                    'loaded' => $offset + $beneficiaries->count()
                ]);
            }

        } catch (\Exception $e) {

            return response()->json([
                'html' => '',
                'total' => 0,
                'loaded' => 0,
                'error' => $e->getMessage()
            ]);
        }
    }

}
