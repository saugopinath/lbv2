<?php

namespace App\Http\Controllers\Home;

use App\Helpers\Helper;
use App\Models\BenEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $financialYear = Helper::getCurrentFinancialYearIndia();

        // ✅ Counts
        // $totalApproved = DB::connection('pgsql_app_read')->table('pension.beneficiaries')->where('next_level_role_id', 0)->count();

        // $totalApproved = DB::connection('pgsql_app_read')->table('pension.beneficiaries')->where('next_level_role_id', 0)->count();


        // $totalApplied = DB::connection('pgsql_app_read')->table('pension.beneficiaries')->where('next_level_role_id', '>=', 0)->count();

        $client = app('elasticsearch');

        $totalApproved = $client->count([
            'index' => 'beneficiaries',
            'body' => [
                'query' => [
                    'term' => [
                        'next_level_role_id' => 0
                    ]
                ]
            ]
        ])['count'];
        // dd($totalApproved);

        $totalApplied = $client->count([
            'index' => 'beneficiaries',
            'body' => [
                'query' => [
                    'range' => [
                        'next_level_role_id' => ['gte' => 0]
                    ]
                ]
            ]
        ])['count'];


        $curMonthInt = (int) date('n');

        // ✅ Get dynamic column name
        $monthPayColumn = Helper::getMonthColumn($curMonthInt);
        $monthPayColumn = $monthPayColumn['lot_payment_amount'];
        // e.g. apr_payment_amount

        // ✅ Current Month Total Payment
        $totalPayCurMonth = DB::connection('pgsql_pay_read')
            ->table('payment.ben_transaction_details')
            ->selectRaw("COALESCE(SUM($monthPayColumn), 0) AS total")
            ->where('fin_year', $financialYear)
            ->value('total');

        // ✅ Current Financial Year Consolidated Payment
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

        // ✅ Calculate total FY amount (IMPORTANT)
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


        return view('dashboard.dashboard', [
            'cur_fin_year' => $financialYear,
            'totalApproved' => $totalApproved,
            'totalApplied' => $totalApplied,
            'totalPayCurMonth' => $totalPayCurMonth,
            'totalPayCurYear' => $totalPayCurYear
        ]);
    }
    public function schemeWiseApplications(Request $request)
    {
        $days = $request->get('days');

        $must = [
            ['range' => ['next_level_role_id' => ['gte' => 0]]]
        ];

        if ($days && $days !== 'all') {
            $must[] = [
                'range' => [
                    'created_at' => [
                        'gte' => "now-{$days}d/d"
                    ]
                ]
            ];
        }

        $response = app('elasticsearch')->search([
            'index' => 'beneficiaries',
            'size' => 0,
            'body' => [
                'query' => ['bool' => ['must' => $must]],
                'aggs' => [
                    'scheme_wise' => [
                        'terms' => [
                            'field' => 'scheme_id',
                            'size' => 50
                        ]
                    ]
                ]
            ]
        ]);

        $categories = [];
        $data = [];

        foreach ($response['aggregations']['scheme_wise']['buckets'] as $bucket) {
            $categories[] = 'Scheme ' . $bucket['key'];
            $data[] = $bucket['doc_count'];
        }

        return response()->json(compact('categories', 'data'));
    }


    public function districtWiseBeneficiaries()
    {
        $response = app('elasticsearch')->search([
            'index' => 'beneficiaries',
            'size' => 0,
            'body' => [
                'query' => [
                    'term' => [
                        'next_level_role_id' => 0
                    ]
                ],
                'aggs' => [
                    'districts' => [
                        'terms' => [
                            'field' => 'district_name',
                            'size' => 50,
                            'order' => ['_count' => 'desc']
                        ]
                    ]
                ]
            ]
        ]);

        $categories = [];
        $data = [];

        foreach ($response['aggregations']['districts']['buckets'] as $bucket) {
            $categories[] = $bucket['key'];
            $data[] = $bucket['doc_count'];
        }

        return response()->json(compact('categories', 'data'));
    }


    public function getAgeDistribution()
    {
        $response = app('elasticsearch')->search([
            'index' => 'beneficiaries',
            'size' => 0,
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            ['range' => ['next_level_role_id' => ['gte' => 0]]],
                            ['exists' => ['field' => 'dob']]
                        ]
                    ]
                ],
                'aggs' => [
                    'age_ranges' => [
                        'range' => [
                            'script' => ['source' => "(new Date().getTime() - doc['dob'].value.toInstant().toEpochMilli())/1000/60/60/24/365"],
                            'ranges' => [
                                ['to' => 18],
                                ['from' => 18, 'to' => 30],
                                ['from' => 30, 'to' => 45],
                                ['from' => 45, 'to' => 60],
                                ['from' => 60]
                            ]
                        ]
                    ]
                ]
            ]
        ]);

        $buckets = $response['aggregations']['age_ranges']['buckets'];

        return response()->json([
            'age_0_18' => $buckets[0]['doc_count'],
            'age_18_30' => $buckets[1]['doc_count'],
            'age_30_45' => $buckets[2]['doc_count'],
            'age_45_60' => $buckets[3]['doc_count'],
            'age_60_plus' => $buckets[4]['doc_count'],
        ]);
    }


    public function consolidatedFyPayments(Request $request)
    {
        $finYear = $request->get('fin_year'); // e.g. 2025-2026

        $data = DB::connection('pgsql_pay_read')->table('payment.ben_transaction_details')
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
