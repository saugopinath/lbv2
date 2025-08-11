<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BeneficiaryListController extends Controller
{
    public function index()
    {
        return view('beneficiaries.index');
    }

    public function show(Request $request)
    {
        $login_type = 'state_office';
        $reportType = $request->input('report_type');

        return view('beneficiaries.report', compact('reportType', 'login_type'));
    }
}
