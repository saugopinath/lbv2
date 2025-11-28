<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MisReportController extends Controller
{
    public function index()
    {
        return view('misreport.report_index');
    }
}
