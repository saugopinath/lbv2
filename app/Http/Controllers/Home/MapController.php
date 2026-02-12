<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class MapController extends Controller
{
    public function index()
    {
        return view('pension.map');
    }




    public function wbDistrictCount(Request $request)
    {
        return DB::connection('pgsql_app_read')->table('pension.beneficiaries')
            ->select('created_by_dist_code as district_code', DB::raw('count(*) total'))
            ->where('next_level_role_id', '=', 0)
            ->groupBy('district_code')
            ->pluck('total', 'district_code');
    }


}