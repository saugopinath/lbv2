<?php

namespace App\Http\Controllers\DynamicWorkflow;

use App\Helpers\WorkFlowPermissionHelper;
use App\Http\Controllers\Controller;
use App\Models\DynamicWorkflowModule;

class UpdateMarkBeneficiaryDetailsController extends Controller
{
    public function index()
    {
        if (WorkFlowPermissionHelper::canUpdateMarkBeneficiaryDetails()) {
            return view('dynamic-workflow.process-workflow-table');
        }
        $header = 'Oops! You do not have permission to view beneficiaries.';

        return view('CommonRestictedpage.index', compact('header'));
    }

    public function listdetails()
    {
        if (WorkFlowPermissionHelper::canUpdateBeneficiaryList()) {
            $moduleCode = 'UP_MB_D_01';
            $module = DynamicWorkflowModule::where('module_code', $moduleCode)->first();
            if (! $module) {
                abort(404, 'Module not found');
            }
            $moduleName = $module->module_name;
            $moduleId = $module->id;

            return view('dynamic-workflow.ListDetails-page', compact('moduleCode', 'module', 'moduleName', 'moduleId'));
        }
        $header = 'Oops! You do not have permission to view beneficiaries.';

        return view('CommonRestictedpage.index', compact('header'));
    }

    public function updateRequest()
    {
        if (WorkFlowPermissionHelper::canRequestUpdateBeneficiary()) {
            $moduleCode = 'UP_MB_D_01';
            $module = DynamicWorkflowModule::where('module_code', $moduleCode)->first();
            if (! $module) {
                // dd('module not found');
                abort(404, 'Module not found');
            }
            $moduleName = $module->module_name;
            $moduleId = $module->id;

            return view('dynamic-workflow.beneficiary-details-update', compact('moduleCode', 'module', 'moduleName', 'moduleId'));
        }
        $header = 'Oops! You do not have permission to view beneficiaries.';

        return view('CommonRestictedpage.index', compact('header'));
    }
}
