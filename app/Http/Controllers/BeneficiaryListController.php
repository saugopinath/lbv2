<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\CheckAuthHelper;
use App\Helpers\WorkFlowPermissionHelper;
use App\Models\Scheme;
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
        // if (Auth::user()->can('view beneficiaries')) {
        if (WorkFlowPermissionHelper::canViewBeneficiaries()) {
            $scheme = Scheme::get();
            return view('beneficiaries.index', compact('scheme'));
        }

        $header = 'Oops! You do not have permission to view beneficiaries.';
        return view('CommonRestictedpage.index', compact('header'));
    }

    /** Report View */

    public function show(Request $request)
    {
        // Backend validation
        $validated = $request->validate([
            'report_type' => 'required|in:1,2,3,4,5,6',
            'scheme_id' => 'required|exists:schemes,id',
        ], [
            'report_type.required' => 'Please select a report type before proceeding.',
            'report_type.in' => 'Invalid report type selected.',
            'scheme_id.required' => 'Please select a scheme before proceeding.',
            'scheme_id.exists' => 'Invalid scheme selected.',
        ]);

        // Permission check
        if (WorkFlowPermissionHelper::canViewReport()) {
            // if (Auth::user()->can('view reports')) {
            $reportType = $validated['report_type'];
            $schemeId = $validated['scheme_id'];
            return view('beneficiaries.report', compact('reportType', 'schemeId'));
        }

        $header = 'Oops! You do not have permission to view reports.';
        return view('CommonRestictedpage.index', compact('header'));
    }
}
