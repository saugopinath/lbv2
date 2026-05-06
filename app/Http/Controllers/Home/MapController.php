<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\BeneficiaryPersonalDetail;
use App\Models\District;

class MapController extends Controller
{
    public function index()
    {
        $districtCount = Cache::remember('map.district.total_count', 600, fn() => District::count());
        return view('frontend.pension.map', compact('districtCount'));
    }




    public function wbDistrictCount(Request $request): JsonResponse
    {
        $data = Cache::remember('map.district.beneficiary_counts', 600, function () {
            return BeneficiaryPersonalDetail::select(
                'created_by_dist_code',
                DB::raw('COUNT(*) as total')
            )
                ->whereIn('is_clean', [1, 2])
                ->whereNotNull('created_by_dist_code')
                ->groupBy('created_by_dist_code')
                ->pluck('total', 'created_by_dist_code');
        });

        return response()->json($data);
    }
}
