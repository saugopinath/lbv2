<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MisReportController extends Controller
{
    public function index()
    {
        $reportTypes = [
            [
                'id'    => 1,
                'name'  => 'Incomplete Details Mis Report',
                'route' => route('incomplete-details-mis-report'),
            ],
        ];

        return view('misreport.report_index', compact('reportTypes'));
    }
}
