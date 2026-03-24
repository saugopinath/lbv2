<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\BeneficiaryPersonalDetail;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    public static function index(Request $request)
    {
        $monthly_disbursement = 7000000000; // 70 crore
        $ben_count = BeneficiaryPersonalDetail::count();
        $scheme_info = Storage::get('data/m_scheme.json');
        $data = json_decode($scheme_info, true);
        $activeSchemes = array_filter($data, function ($item) {
            return $item['is_active'] == 1;
        });
        $department_info = Storage::get('data/m_department.json');
        $data = json_decode($department_info, true);
        $activeDepartment = array_filter($data, function ($item) {
            return $item['is_active'] == 1;
        });

        $total_dept = count($activeDepartment);
        $total_schemes = count($activeSchemes);

        // dd($department);
        return view('frontend.home.home', [
            'ben_count' => $ben_count,
            'total_dept' => $total_dept,
            'total_schemes' => $total_schemes,
            'monthly_disbursement' => $monthly_disbursement,
            'scheme_info' => $activeSchemes,
            'department' => $activeDepartment
        ]);
    }
    public static function scheme_index(Request $request)
    {
        $scheme_slug = $request->scheme;
        $scheme_info = DB::table('home.m_scheme')->where('slug', $scheme_slug)->first();
        $scheme_config = json_decode($scheme_info->json_data);
        $scheme_id = $scheme_info->id;
        $ben_count = 100000000000;
        $department = DB::table('home.m_department')->where('id', $scheme_info->department_id)->first();
        return view('frontend.home.scheme-info', [
            'scheme_info' => $scheme_info,
            'scheme_json' => $scheme_config,
            'ben_count' => $ben_count,
            'department' => $department,
            'scheme_id' => $scheme_id
        ]);

    }

    public static function department_index(Request $request)
    {
        // dd($request->all());
        $dept = $request->dept;
        $department_info = DB::table('home.m_department')->where('slug', $dept)->first();
        $dept_id = $department_info->id;
        $department_json = json_decode($department_info->json_data);
        $onboard_scheme = DB::table('home.m_scheme')->where('is_active', 1)->where('department_id', $dept_id)->pluck('id', 'scheme_name')->toArray();

        $scheme_id = array();
        foreach ($onboard_scheme as $key => $value) {
            $scheme_id[] = $value; // value itself is the ID
        }

        $ben_count_all = BeneficiaryPersonalDetail::whereIn('is_clean', [1, 2])->count();
        $ben_count_approved = BeneficiaryPersonalDetail::whereIn('is_clean', [1, 2])->where('is_approved', 1)->count();

        $onboard_scheme_count = count($onboard_scheme);

        $total_disbrusment = 3200000000;
        return view('frontend.home.department-info', [
            'department_info' => $department_info,
            'department_json' => $department_json,
            'ben_count_all' => $ben_count_all,
            'ben_count_approved' => $ben_count_approved,
            'onboard_scheme_count' => $onboard_scheme_count,
            'total_disbrusment' => $total_disbrusment
        ]);
    }
}
