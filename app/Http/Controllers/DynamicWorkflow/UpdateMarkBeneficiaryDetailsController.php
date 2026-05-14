<?php

namespace App\Http\Controllers\DynamicWorkflow;
use App\Http\Controllers\Controller;
use App\Models\DynamicWorkflowModule;
use Illuminate\Http\Request;

class UpdateMarkBeneficiaryDetailsController extends Controller
{
    public function index()
    {
        return view('dynamic-workflow.process-workflow-table');
    }

    public function listdetails()
    {
        $moduleCode = config('constants.module_codes.update_mark_beneficiary');
        $module = DynamicWorkflowModule::where('module_code', $moduleCode)->first();
        if (!$module) {
            abort(404, 'Module not found');
        }
        $moduleName = $module->module_name;
        $moduleId = $module->id;

        return view('dynamic-workflow.ListDetails-page', compact('moduleCode', 'module', 'moduleName', 'moduleId'));
    }
    public function updateRequest()
    {
        $moduleCode = config('constants.module_codes.update_mark_beneficiary');
        $module = DynamicWorkflowModule::where('module_code', $moduleCode)->first();
        if (!$module) {
            // dd('module not found');
            abort(404, 'Module not found');
        }
        $moduleName = $module->module_name;
        $moduleId = $module->id;

        return view('dynamic-workflow.beneficiary-details-update', compact('moduleCode', 'module', 'moduleName', 'moduleId'));
    }
}
