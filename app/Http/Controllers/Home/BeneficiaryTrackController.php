<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Models\BenDocs;
use App\Models\Beneficiary;
use App\Models\BeneficiaryJBLB;
use App\Models\BeneficiaryPersonalDetail;
use App\Models\BenEntry;
use App\Models\Block;
use App\Models\District;
use App\Models\GP;
use App\Models\Scheme;
use App\Models\Taluka;
use App\Models\UrbanBody;
use App\Models\Ward;
use App\Models\DocumentType;
use App\Models\Municipality;
use App\Models\Panchayat;
use App\Models\Subdivision;
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
        $results = BeneficiaryPersonalDetail::search('*')->get()->count();

        // Fetch Location Data for Scripts
        $blocks = Block::select('id  as id', 'name as name', 'district_id')
            ->get();

        $subDistricts = Subdivision::select('id as id', 'name as name', 'district_id')
            ->get();

        $ulbs = Municipality::select('id as id', 'name as name', 'subdivision_id')
            ->get();

        $gps = Panchayat::select('id as id', 'name as name', 'block_id')
            ->get();

        $ulb_wards = Ward::select('id as id', 'name as name', 'municipality_id')
            ->get();

        return view('frontend.track-ben.ben-track', compact(
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
                $scout = BeneficiaryPersonalDetail::search($search ?: '');

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
                $html = '';

                foreach ($beneficiaries as $b) {

                    // 🔥 No more DB queries if you include names in index
                    $districtName = $b->district_name ?? 'Unknown';
                    $schemeName = Scheme::where('id', $b->scheme_id)->first()->name ?? 'Unknown';

                    $status = $b->next_level_role_id == 0 ? 'Approved' : 'Approval Pending';
                    $statusClass = $b->next_level_role_id == 0
                        ? 'status-active'
                        : 'status-pending';

                    $html .= view('frontend.track-ben.beneficiary-card', [
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
                    'loaded' => $offset + count($beneficiaries->items())
                ]);
            }
        } catch (\Throwable $e) {

            return response()->json([
                'html' => '',
                'total' => 0,
                'loaded' => 0,
                'error' => $e->getMessage()
            ]);
        }
    }
}
