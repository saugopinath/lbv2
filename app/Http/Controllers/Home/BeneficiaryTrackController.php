<?php

namespace App\Http\Controllers\Home;

use App\Models\BenDocs;
use App\Models\Beneficiary;
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
use App\Http\Controllers\Controller;

class BeneficiaryTrackController extends Controller
{
    public function trackBeneficiary(Request $request)
    {
        $schemes = Scheme::where('is_active', 1)->get();
        $districts = District::all();
        $results = Beneficiary::search('*')->get()->count();

        if ($request->ajax()) {

            $limit = 100;
            $offset = (int) $request->get('offset', 0);

            $query = Beneficiary::query();

            // 🔍 Search
            if ($search = $request->search) {
                $query->where(function ($q) use ($search) {
                    $q->where('ben_fname', 'ILIKE', "%$search%")
                        ->orWhere('ben_lname', 'ILIKE', "%$search%")
                        ->orWhere('mobile_no', 'ILIKE', "%$search%")
                        ->orWhere('id', 'ILIKE', "%$search%")
                        ->orWhere('aadhar_no', 'ILIKE', "%$search%")
                        ->orWhere('bank_code', 'ILIKE', "%$search%");
                });
            }

            // 🎯 Filters
            if ($request->scheme) {
                $query->where('scheme_id', $request->scheme);
            }

            if ($request->district) {
                $query->where('created_by_dist_code', $request->district);
            }

            if ($request->urban_code) {
                $query->where('rural_urban_id', $request->urban_code);
            }

            $total = $query->count();

            $beneficiaries = $query
                ->offset($offset)
                ->limit($limit)
                ->orderBy('id', 'desc')
                ->get();

            $html = '';

            foreach ($beneficiaries as $b) {

                $districtName = District::where('district_code', $b->created_by_dist_code)
                    ->value('district_name') ?? 'Unknown';

                $schemeName = Scheme::where('id', $b->scheme_id)
                    ->value('scheme_name') ?? 'Unknown';


                $status = '';
                $statusClass = '';
                if ($b->next_level_role_id == 0) {
                    $status = 'Approved';
                    $statusClass = 'status-active';
                } else {
                    $status = 'Approval Pending';
                    $statusClass = 'status-pending';
                }

                $html .= view('track-ben.beneficiary-card', [
                    'status' => $status,
                    'statusClass' => $statusClass,
                    'beneficiaryId' => $b->id,
                    'name' => trim($b->ben_fname . ' ' . $b->ben_lname),
                    'relation' => 'Father',
                    'relationName' => $b->father_name ?? 'N/A',
                    'schemeName' => $schemeName,
                    'location' => $districtName . ', West Bengal',
                    'mobile' => $b->mobile_no ?? 'N/A'
                ])->render();
            }

            return response()->json([
                'html' => $html,
                'total' => $total,
                'loaded' => $offset + $beneficiaries->count()
            ]);
        }

        return view('track-ben.ben-track', compact('schemes', 'districts', 'results'));
    }
}
