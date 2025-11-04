<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\CheckAuthHelper;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;

class BeneficiaryListController extends Controller
{
    protected $isAuthorized = false;
    public function __construct()
    {
        if (CheckAuthHelper::isCommonWorkFlow4thStep()) {
            $this->isAuthorized = true;
        } else {
            redirect()->route('dashboard')
                ->with('error', 'Oops! You are not authorized to perform this action.')
                ->send();
        }
    }

    /** Beneficiary List View */
    public function index()
    {
        if (Auth::user()->can('view beneficiaries')) {
            return view('beneficiaries.index');
        }

        $header = 'Oops! You do not have permission to view beneficiaries.';
        return view('CommonRestictedpage.index', compact('header'));
    }

    /** Report View */

    public function show(Request $request)
    {
        // Backend validation
        $validated = $request->validate([
            'report_type' => 'required|in:1,2,3,4,5',
        ], [
            'report_type.required' => 'Please select a report type before proceeding.',
            'report_type.in' => 'Invalid report type selected.',
        ]);

        // Permission check
        if (Auth::user()->can('view reports')) {
            $reportType = $validated['report_type'];
            return view('beneficiaries.report', compact('reportType'));
        }

        $header = 'Oops! You do not have permission to view reports.';
        return view('CommonRestictedpage.index', compact('header'));
    }
}
