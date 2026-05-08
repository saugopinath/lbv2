<?php

namespace App\Http\Controllers;

use App\Helpers\CheckAuthHelper;
use App\Helpers\WorkFlowPermissionHelper;
use App\Models\SchemeFinalSubmitCheck;
use Illuminate\Http\Request;

class BeneficiaryListController extends Controller
{
    protected $isAuthorized = false;

    protected $schemes = [];

    protected $reportTypes = [];

    public function __construct()
    {
        $this->schemes = SchemeFinalSubmitCheck::where('is_final_submitted', true)
            ->whereHas('scheme')
            ->with('scheme')
            ->get()
            ->pluck('scheme')
            ->unique('id')
            ->values();
        $verifier = CheckAuthHelper::isCommmonVerifier();
        $approver = CheckAuthHelper::isCommonApprover();
        $this->reportTypes = [];
        if ($verifier) {
            $this->reportTypes = [
                '2' => 'Verified List',
                '4' => 'Rejected List',
                '5' => 'Reverted List',
                '3' => 'Approved List',
            ];
        } elseif ($approver) {
            $this->reportTypes = [
                '3' => 'Approved List',
                '4' => 'Rejected List',
                '5' => 'Reverted List',
            ];
        } else {
            $this->reportTypes = [
                '1' => 'Partial Entry List',
                '2' => 'Verified List',
                '3' => 'Approved List',
                '4' => 'Rejected List',
                '5' => 'Reverted List',
                '6' => 'Submitted List',
            ];
        }
        if (CheckAuthHelper::isCommonWorkFlow4thStep()) {
            $this->isAuthorized = true;
        } else {
            redirect()->route('dashboard')
                ->with('error', 'Oops! You are not authorized to perform this action.')
                ->send();
        }
    }

    public function index()
    {
        if (WorkFlowPermissionHelper::canViewBeneficiaries()) {
            $schemes = $this->schemes;
            $reporttypes = $this->reportTypes;

            return view('beneficiaries.index', compact('schemes', 'reporttypes'));
        }
        $header = 'Oops! You do not have permission to view beneficiaries.';

        return view('CommonRestictedpage.index', compact('header'));
    }
    
    public function show(Request $request)
    {
        $validated = $request->validate([
            'report_type' => 'required|in:1,2,3,4,5,6',
            'scheme' => 'required',
        ], [
            'report_type.required' => 'Please select a report type before proceeding.',
            'report_type.in' => 'Invalid report type selected.',
        ]);

        if (WorkFlowPermissionHelper::canViewReport()) {
            $reportType = $validated['report_type'];
            $scheme = $validated['scheme'];

            return view('beneficiaries.report', compact('reportType', 'scheme'));
        }

        $header = 'Oops! You do not have permission to view reports.';

        return view('CommonRestictedpage.index', compact('header'));
    }
}
