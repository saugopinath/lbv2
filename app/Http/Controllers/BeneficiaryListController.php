<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;

class BeneficiaryListController extends Controller
{
    public function index()
    {
        return view('beneficiaries.index');
    }

    public function show(Request $request)
    {
        $reportType = $request->input('report_type');

        return view('beneficiaries.report', compact('reportType'));
    }
}
