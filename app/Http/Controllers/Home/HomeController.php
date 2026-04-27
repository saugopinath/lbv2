<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BeneficiaryPersonalDetail;
use App\Models\Notification;
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
        $data = json_decode(Storage::get('data/master_data.json'), true);
        $notifications = Notification::where('status', 'active')
            ->orderBy('notified_at', 'desc')
            ->take(10)
            ->get();

        // dd($department);
        return view('frontend.home.home', [
            'ben_count' => $ben_count,
            'total_dept' => $total_dept,
            'total_schemes' => $total_schemes,
            'monthly_disbursement' => $monthly_disbursement,
            'scheme_info' => $activeSchemes,
            'department' => $activeDepartment,
            'data' => $data,
            'notifications' => $notifications
        ]);
    }
    public static function scheme_index(Request $request)
    {
        $scheme_slug = $request->scheme;

        // Decode as objects (no `true`) so Blade can use -> syntax (e.g. $scheme_info->scheme_name)
        $schemes = json_decode(Storage::get('data/m_scheme.json'));

        // Re-index so [0] is safe regardless of position in the JSON file
        $matched = array_values(array_filter($schemes, function ($item) use ($scheme_slug) {
            return $item->slug == $scheme_slug;
        }));

        if (empty($matched)) {
            abort(404, 'Scheme not found.');
        }

        $scheme = $matched[0];           // stdClass object
        $scheme_config = $scheme->json_data;    // already a stdClass (nested object)
        $scheme_id = $scheme->id;
        $ben_count = 100000000000;

        // Fetch department from JSON as objects too (Blade uses $department->f_name etc.)
        $departments = json_decode(Storage::get('data/m_department.json'));
        $dept_matched = array_values(array_filter($departments, function ($item) use ($scheme) {
            return $item->id == $scheme->department_id;
        }));
        $department = $dept_matched[0] ?? null;

        return view('frontend.home.scheme-info', [
            'scheme_info' => $scheme,
            'scheme_json' => $scheme_config,
            'ben_count' => $ben_count,
            'department' => $department,
            'scheme_id' => $scheme_id,
        ]);
    }


    public static function department_index($department)
    {
        // Decode departments as objects so Blade -> syntax works
        $departments = json_decode(Storage::get('data/m_department.json'));

        $dept_matched = array_values(array_filter($departments, function ($item) use ($department) {
            return $item->slug == $department;
        }));

        if (empty($dept_matched)) {
            abort(404, 'Department not found.');
        }

        $department_info = $dept_matched[0];
        $dept_id = $department_info->id;

        // json_data is already a stdClass (decoded above without `true`) — no need to json_decode again
        $department_json = $department_info->json_data;

        // Fetch onboard schemes from JSON (consistent — no DB call needed)
        $all_schemes = json_decode(Storage::get('data/m_scheme.json'));
        $onboard_scheme = array_values(array_filter($all_schemes, function ($item) use ($dept_id) {
            return $item->is_active == 1 && $item->department_id == $dept_id;
        }));

        $ben_count_all = BeneficiaryPersonalDetail::whereIn('is_clean', [1, 2])->count();
        $ben_count_approved = BeneficiaryPersonalDetail::whereIn('is_clean', [1, 2])->count();

        $onboard_scheme_count = count($onboard_scheme);
        $total_disbrusment = 3200000000;
        // var_dump($department_json);
        // dd($department_info, $department_json);
        return view('frontend.home.department-info', [
            'department_info' => $department_info,
            'department_json' => $department_json,
            'ben_count_all' => $ben_count_all,
            'ben_count_approved' => $ben_count_approved,
            'onboard_scheme_count' => $onboard_scheme_count,
            'total_disbrusment' => $total_disbrusment,
        ]);
    }
}
