<?php

namespace App\Http\Controllers\Home;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\BeneficiaryPersonalDetail;
use App\Models\WorkflowsteproleMapping;



class DashboardController extends Controller
{

    public array $schemeIds = [];
    public string $appPortal;

    public function __construct()
    {
        $this->schemeIds = config('jblbConf.schemeIds', []);
        $this->appPortal = config('jblbConf.app_portal');
    }

    public function index(Request $request)
    {
        $financialYear = Helper::getCurrentFinancialYearIndia();

        // Fetch scheme → final-approval-role map. Skip schemes with no final step.
        $approveRoles = $this->approveRoleId();

        // Count beneficiaries whose current workflow position matches the final
        // approval role of their respective scheme (i.e. fully approved).
        if (!empty($approveRoles)) {
            $totalApproved = DB::connection('pgsql_app_read')
                ->table('pension.beneficiary_personals')
                ->whereIn('scheme_id', $this->schemeIds)
                ->where(function ($query) use ($approveRoles) {
                    foreach ($approveRoles as $schemeId => $roleId) {
                        $query->orWhere(function ($q) use ($schemeId, $roleId) {
                            $q->where('scheme_id', $schemeId)
                                ->where('next_level_role_id', $roleId);
                        });
                    }
                })
                ->count();
        } else {
            $totalApproved = 0;
        }

        // All submitted applications (next_level_role_id > 0 means past entry stage)
        $totalApplied = DB::connection('pgsql_app_read')
            ->table('pension.beneficiary_personals')
            ->whereIn('scheme_id', $this->schemeIds)
            ->where('next_level_role_id', '>=', 0)
            ->count();

        $curMonthInt = (int) date('n');

        $monthPayColumn = Helper::getMonthColumn($curMonthInt);
        $monthPayColumn = $monthPayColumn['lot_payment_amount'];

        $totalPayCurMonth = DB::connection('pgsql_pay_read')
            ->table('payment.ben_transaction_details')
            ->selectRaw("COALESCE(SUM($monthPayColumn), 0) AS total")
            ->where('fin_year', $financialYear)
            ->when(!in_array(20, $this->schemeIds), function ($query) {
                $query->whereIn('scheme_id', $this->schemeIds);
            })
            ->value('total') ?? 0;

        $totalPayCurYearRow = DB::connection('pgsql_pay_read')
            ->table('payment.ben_transaction_details')
            ->selectRaw("
                COALESCE(SUM(apr_payment_amount),0) AS apr,
                COALESCE(SUM(may_payment_amount),0) AS may,
                COALESCE(SUM(jun_payment_amount),0) AS jun,
                COALESCE(SUM(jul_payment_amount),0) AS jul,
                COALESCE(SUM(aug_payment_amount),0) AS aug,
                COALESCE(SUM(sep_payment_amount),0) AS sep,
                COALESCE(SUM(oct_payment_amount),0) AS oct,
                COALESCE(SUM(nov_payment_amount),0) AS nov,
                COALESCE(SUM(dec_payment_amount),0) AS dec,
                COALESCE(SUM(jan_payment_amount),0) AS jan,
                COALESCE(SUM(feb_payment_amount),0) AS feb,
                COALESCE(SUM(mar_payment_amount),0) AS mar
            ")
            ->where('fin_year', $financialYear)
            ->when(!in_array(20, $this->schemeIds), function ($query) {
                $query->whereIn('scheme_id', $this->schemeIds);
            })
            ->first();

        // Guard against null result (e.g. empty payment DB)
        $totalPayCurYear = $totalPayCurYearRow
            ? (
                $totalPayCurYearRow->apr
                + $totalPayCurYearRow->may
                + $totalPayCurYearRow->jun
                + $totalPayCurYearRow->jul
                + $totalPayCurYearRow->aug
                + $totalPayCurYearRow->sep
                + $totalPayCurYearRow->oct
                + $totalPayCurYearRow->nov
                + $totalPayCurYearRow->dec
                + $totalPayCurYearRow->jan
                + $totalPayCurYearRow->feb
                + $totalPayCurYearRow->mar
            )
            : 0;

        return view('frontend.dashboard.dashboard', [
            'cur_fin_year' => $financialYear,
            'totalApproved' => $totalApproved,
            'totalApplied' => $totalApplied,
            'totalPayCurMonth' => $totalPayCurMonth,
            'totalPayCurYear' => $totalPayCurYear,
        ]);
    }

    public function schemeWiseApplications(Request $request)
    {
        $days = $request->get('days');
        $approveRoles = $this->approveRoleId();

        if (empty($approveRoles)) {
            return response()->json(['categories' => [], 'data' => []]);
        }

        $query = DB::connection('pgsql_app_read')
            ->table('pension.beneficiary_personals')
            ->selectRaw('scheme_id, COUNT(*) as total')
            ->where(function ($query) use ($approveRoles) {
                foreach ($approveRoles as $schemeId => $roleId) {
                    $query->orWhere(function ($q) use ($schemeId, $roleId) {
                        $q->where('scheme_id', $schemeId)
                            ->where('next_level_role_id', $roleId);
                    });
                }
            })
            ->groupBy('scheme_id')
            ->orderBy('scheme_id');

        if ($days && $days !== 'all') {
            $query->where('created_at', '>=', now()->subDays((int) $days));
        }

        $rows = $query->get();

        $categories = [];
        $data = [];

        foreach ($rows as $row) {
            $categories[] = 'Scheme ' . $row->scheme_id;
            $data[] = (int) $row->total;
        }

        return response()->json(compact('categories', 'data'));
    }

    public function districtWiseBeneficiaries()
    {
        $approveRoles = $this->approveRoleId();

        if (empty($approveRoles)) {
            return response()->json(['categories' => [], 'data' => []]);
        }

        $rows = DB::connection('pgsql_app_read')
            ->table('pension.beneficiary_personals as b')
            ->join('public.districts AS d', DB::raw('CAST(b.created_by_dist_code AS TEXT)'), '=', DB::raw('CAST(d.lgd_code AS TEXT)'))
            ->selectRaw('d.name, COUNT(*) as total')
            ->where(function ($query) use ($approveRoles) {
                foreach ($approveRoles as $schemeId => $roleId) {
                    $query->orWhere(function ($q) use ($schemeId, $roleId) {
                        $q->where('scheme_id', $schemeId)
                            ->where('next_level_role_id', $roleId);
                    });
                }
            })
            ->groupBy('d.name')
            ->orderByDesc('total')
            ->limit(50)
            ->get();

        $categories = $rows->pluck('name')->toArray();
        $data = $rows->pluck('total')->map(fn($v) => (int) $v)->toArray();

        return response()->json(compact('categories', 'data'));
    }

    public function getAgeDistribution()
    {
        $row = DB::connection('pgsql_app_read')
            ->table('pension.beneficiary_personals')
            ->selectRaw("
                COUNT(*) FILTER (WHERE EXTRACT(YEAR FROM AGE(CURRENT_DATE, dob)) < 18)            AS age_0_18,
                COUNT(*) FILTER (WHERE EXTRACT(YEAR FROM AGE(CURRENT_DATE, dob)) BETWEEN 18 AND 29) AS age_18_30,
                COUNT(*) FILTER (WHERE EXTRACT(YEAR FROM AGE(CURRENT_DATE, dob)) BETWEEN 30 AND 44) AS age_30_45,
                COUNT(*) FILTER (WHERE EXTRACT(YEAR FROM AGE(CURRENT_DATE, dob)) BETWEEN 45 AND 59) AS age_45_60,
                COUNT(*) FILTER (WHERE EXTRACT(YEAR FROM AGE(CURRENT_DATE, dob)) >= 60)            AS age_60_plus
            ")
            ->whereNotNull('dob')
            ->where('next_level_role_id', '>', 0)
            ->whereIn('scheme_id', $this->schemeIds)
            ->first();

        return response()->json([
            'age_0_18' => (int) ($row->age_0_18 ?? 0),
            'age_18_30' => (int) ($row->age_18_30 ?? 0),
            'age_30_45' => (int) ($row->age_30_45 ?? 0),
            'age_45_60' => (int) ($row->age_45_60 ?? 0),
            'age_60_plus' => (int) ($row->age_60_plus ?? 0),
        ]);
    }

    public function consolidatedFyPayments(Request $request)
    {
        $finYear = $request->get('fin_year');

        // Validate fin_year to prevent injection
        if (!$finYear || !preg_match('/^\d{4}-\d{4}$/', $finYear)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid financial year'], 422);
        }

        $data = DB::connection('pgsql_pay_read')
            ->table('payment.ben_transaction_details')
            ->selectRaw("
                COALESCE(SUM(apr_payment_amount),0) AS apr,
                COALESCE(SUM(may_payment_amount),0) AS may,
                COALESCE(SUM(jun_payment_amount),0) AS jun,
                COALESCE(SUM(jul_payment_amount),0) AS jul,
                COALESCE(SUM(aug_payment_amount),0) AS aug,
                COALESCE(SUM(sep_payment_amount),0) AS sep,
                COALESCE(SUM(oct_payment_amount),0) AS oct,
                COALESCE(SUM(nov_payment_amount),0) AS nov,
                COALESCE(SUM(dec_payment_amount),0) AS dec,
                COALESCE(SUM(jan_payment_amount),0) AS jan,
                COALESCE(SUM(feb_payment_amount),0) AS feb,
                COALESCE(SUM(mar_payment_amount),0) AS mar
            ")
            ->where('fin_year', $finYear)
            ->when(!in_array(20, $this->schemeIds), function ($query) {
                $query->whereIn('scheme_id', $this->schemeIds);
            })
            ->first();

        return response()->json([
            'status' => 'success',
            'series' => [
                (int) ($data->apr ?? 0),
                (int) ($data->may ?? 0),
                (int) ($data->jun ?? 0),
                (int) ($data->jul ?? 0),
                (int) ($data->aug ?? 0),
                (int) ($data->sep ?? 0),
                (int) ($data->oct ?? 0),
                (int) ($data->nov ?? 0),
                (int) ($data->dec ?? 0),
                (int) ($data->jan ?? 0),
                (int) ($data->feb ?? 0),
                (int) ($data->mar ?? 0),
            ]
        ]);
    }

    public function maritalStatusDistribution()
    {
        $rows = DB::connection('pgsql_app_read')
            ->table('pension.beneficiary_personals')
            ->selectRaw("COALESCE(CAST(mar_statu AS VARCHAR), 'Unknown') as name, COUNT(*) as y")
            ->where('next_level_role_id', '>', 0)
            ->whereIn('scheme_id', $this->schemeIds)
            ->groupBy('mar_statu')
            ->get();

        // Optional mapping if mar_statu is stored as numeric/code
        $map = [
            '1' => 'Unmarried',
            '2' => 'Married',
            '3' => 'Widow',
            '4' => 'Divorced',
            '5' => 'Separated',
            'Unmarried' => 'Unmarried',
            'Married' => 'Married',
            'Widow' => 'Widow',
            'Divorced' => 'Divorced',
            'Separated' => 'Separated',
        ];

        $data = $rows->map(function ($row) use ($map) {
            return [
                'name' => $map[$row->name] ?? $row->name,
                'y' => $row->y
            ];
        });

        return response()->json($data);
    }

    public function casteDistribution()
    {
        $rows = DB::connection('pgsql_app_read')
            ->table('pension.beneficiary_personals')
            ->selectRaw("COALESCE(CAST(caste AS VARCHAR), 'Others') as name, COUNT(*) as y")
            ->where('next_level_role_id', '>', 0)
            ->whereIn('scheme_id', $this->schemeIds)
            ->groupBy('caste')
            ->get();

        return response()->json($rows);
    }

    public function dailyApplications()
    {
        // Get last 30 days of application counts
        $rows = DB::connection('pgsql_app_read')
            ->table('pension.beneficiary_personals')
            ->selectRaw("DATE(created_at) as date, COUNT(*) as total")
            ->where('created_at', '>=', now()->subDays(30)->startOfDay())
            ->whereIn('scheme_id', $this->schemeIds)
            ->groupBy(DB::raw("DATE(created_at)"))
            ->orderBy('date', 'ASC')
            ->get();

        $categories = $rows->pluck('date')->map(fn($d) => date('d M', strtotime($d)))->toArray();
        $data = $rows->pluck('total')->map(fn($v) => (int) $v)->toArray();

        return response()->json(compact('categories', 'data'));
    }

    public function refreshMV(Request $request)
    {
        if (config('app.app_portal') == 'jb') {
            DB::connection('pgsql_app_read')->statement('REFRESH MATERIALIZED VIEW CONCURRENTLY pension.mv_scheme_status_summary_v1');
        } else {
            DB::connection('pgsql_app_read')->statement('REFRESH MATERIALIZED VIEW CONCURRENTLY pension.mv_scheme_status_summary_v2');
        }
        return response()->json(['status' => 'success']);
    }

    /**
     * Returns a map of [ scheme_id => final_approval_role_id ].
     * Schemes without a configured final step are included as null.
     */
    public function approveRoleId(): array
    {
        $roles = [];

        foreach ($this->schemeIds as $id) {
            $role = WorkflowsteproleMapping::where('scheme_id', $id)
                ->where('is_final_step', true)
                ->first();

            if ($role?->next_level_role_id !== null) {
                $roles[$id] = $role->next_level_role_id;
            }
        }

        return $roles;
    }
}
