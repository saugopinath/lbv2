<?php

namespace App\Http\Controllers\DynamicWorkflow;

use App\Helpers\WorkFlowPermissionHelper;
use App\Http\Controllers\Controller;
use App\Models\DynamicWorkflowModule;

class CasteManagementController extends Controller
{
    public function index()
    {
        $schemeId = WorkFlowPermissionHelper::getSchemeId();
        if (WorkFlowPermissionHelper::canModifyCaste($schemeId)) {
            // if (Auth::user()->can('modify caste')) {
            $header = 'Caste Modification Information';
            $moduleCode = config('constants.module_codes.caste_management');
            $mainModule = DynamicWorkflowModule::where('module_code', $moduleCode)->first();
            if (! $mainModule) {
                $header = 'Oops! You do not configure this module.';

                return view('CommonRestictedpage.index', compact('header'));
            }
            // dd($mainModule);
            $moduleName = $mainModule->module_name;
            $mainModuleId = $mainModule->id;

            return view('dynamic-workflow.caste-managment.index', compact('header', 'moduleCode', 'moduleName', 'mainModuleId'));
        }
        $header = 'Oops! You do not have permission to modify caste.';

        return view('CommonRestictedpage.index', compact('header'));
    }

    public function requestdedlistdetails()
    {
        $schemeId = WorkFlowPermissionHelper::getSchemeId();
        if (WorkFlowPermissionHelper::canCasteModification($schemeId)) {
            // if (Auth::user()->can('modify caste')) {
            $header = 'Caste Modification Information';
            $moduleCode = config('constants.module_codes.caste_management');
            $mainModule = DynamicWorkflowModule::where('module_code', $moduleCode)->first();
            if (! $mainModule) {
                $header = 'Oops! You do not configure this module.';

                return view('CommonRestictedpage.index', compact('header'));
            }
            // dd($mainModule);
            $moduleName = $mainModule->module_name;
            $mainModuleId = $mainModule->id;

            return view('dynamic-workflow.caste-managment.requested-list-details-page', compact('header', 'moduleCode', 'moduleName', 'mainModuleId'));
        }
        $header = 'Oops! You do not have permission to modify caste.';

        return view('CommonRestictedpage.index', compact('header'));
    }

    public function updateDetails()
    {
        return view('dynamic-workflow.caste-managment.updateDetails');
    }
}
