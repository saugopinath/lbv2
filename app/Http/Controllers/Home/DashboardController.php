<?php

namespace App\Http\Controllers\Home;

use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{


    public function index(Request $request)
    {
        $financialYear = Helper::getCurrentFinancialYearIndia();

        // ✅ Total Approved (next_level_role_id = 0 means fully approved)
        $totalApproved = DB::connection('pgsql_app_read')
            ->table('pension.beneficiary_personals')
            ->where('next_level_role_id', 0)
            ->count();

        // ✅ Total Applied (next_level_role_id >= 0)
        $totalApplied = DB::connection('pgsql_app_read')
            ->table('pension.beneficiary_personals')
            ->where('next_level_role_id', '>=', 0)
            ->count();

        $curMonthInt = (int) date('n');

        // ✅ Get dynamic column name
        $monthPayColumn = Helper::getMonthColumn($curMonthInt);
        $monthPayColumn = $monthPayColumn['lot_payment_amount'];

        // ✅ Current Month Total Payment
        $totalPayCurMonth = DB::connection('pgsql_pay_read')
            ->table('payment.ben_transaction_details')
            ->selectRaw("COALESCE(SUM($monthPayColumn), 0) AS total")
            ->where('fin_year', $financialYear)
            ->value('total');

        // ✅ Financial Year Consolidated (month-wise)
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
            ->first();

        // ✅ Calculate total FY amount
        $totalPayCurYear =
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
            + $totalPayCurYearRow->mar;

        return view('frontend.dashboard.dashboard', [
            'cur_fin_year'     => $financialYear,
            'totalApproved'    => $totalApproved,
            'totalApplied'     => $totalApplied,
            'totalPayCurMonth' => $totalPayCurMonth,
            'totalPayCurYear'  => $totalPayCurYear,
        ]);
    }

    public function schemeWiseApplications(Request $request)
    {
        $days = $request->get('days');

        $query = DB::connection('pgsql_app_read')
            ->table('pension.beneficiary_personals')
            ->selectRaw('scheme_id, COUNT(*) as total')
            ->where('next_level_role_id', '>=', 0)
            ->groupBy('scheme_id')
            ->orderBy('scheme_id');

        if ($days && $days !== 'all') {
            $query->where('created_at', '>=', now()->subDays((int) $days));
        }

        $rows = $query->get();

        $categories = [];
        $data       = [];

        foreach ($rows as $row) {
            $categories[] = 'Scheme ' . $row->scheme_id;
            $data[]       = (int) $row->total;
        }

        return response()->json(compact('categories', 'data'));
    }

    public function districtWiseBeneficiaries()
    {
        // ✅ DB query — district_name is not a Meilisearch filterable/facetable attribute
        $rows = DB::connection('pgsql_app_read')
            ->table('pension.beneficiary_personals as b')
            ->joinRaw('INNER JOIN public.districts AS d ON CAST(b.created_by_dist_code AS TEXT) = CAST(d.lgd_code AS TEXT)')
            ->selectRaw('d.local_name, COUNT(*) as total')
            ->where('b.next_level_role_id', 0)
            ->groupBy('d.local_name')
            ->orderByDesc('total')
            ->limit(50)
            ->get();

        $categories = $rows->pluck('local_name')->toArray();
        $data       = $rows->pluck('total')->map(fn($v) => (int) $v)->toArray();

        return response()->json(compact('categories', 'data'));
    }

    public function getAgeDistribution()
    {
        // ✅ DB query — Meilisearch cannot perform script-based age calculations
        $row = DB::connection('pgsql_app_read')
            ->table('pension.beneficiaries')
            ->selectRaw("
                COUNT(*) FILTER (WHERE EXTRACT(YEAR FROM AGE(CURRENT_DATE, dob)) < 18)            AS age_0_18,
                COUNT(*) FILTER (WHERE EXTRACT(YEAR FROM AGE(CURRENT_DATE, dob)) BETWEEN 18 AND 29) AS age_18_30,
                COUNT(*) FILTER (WHERE EXTRACT(YEAR FROM AGE(CURRENT_DATE, dob)) BETWEEN 30 AND 44) AS age_30_45,
                COUNT(*) FILTER (WHERE EXTRACT(YEAR FROM AGE(CURRENT_DATE, dob)) BETWEEN 45 AND 59) AS age_45_60,
                COUNT(*) FILTER (WHERE EXTRACT(YEAR FROM AGE(CURRENT_DATE, dob)) >= 60)            AS age_60_plus
            ")
            ->whereNotNull('dob')
            ->where('next_level_role_id', '>=', 0)
            ->first();

        return response()->json([
            'age_0_18'    => (int) $row->age_0_18,
            'age_18_30'   => (int) $row->age_18_30,
            'age_30_45'   => (int) $row->age_30_45,
            'age_45_60'   => (int) $row->age_45_60,
            'age_60_plus' => (int) $row->age_60_plus,
        ]);
    }

    public function consolidatedFyPayments(Request $request)
    {
        $finYear = $request->get('fin_year');

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
            ->first();

        return response()->json([
            'status' => 'success',
            'series' => [
                (int) $data->apr,
                (int) $data->may,
                (int) $data->jun,
                (int) $data->jul,
                (int) $data->aug,
                (int) $data->sep,
                (int) $data->oct,
                (int) $data->nov,
                (int) $data->dec,
                (int) $data->jan,
                (int) $data->feb,
                (int) $data->mar,
            ]
        ]);
    }
}
