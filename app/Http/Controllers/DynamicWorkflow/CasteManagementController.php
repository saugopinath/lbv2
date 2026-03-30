<?php

namespace App\Http\Controllers\DynamicWorkflow;

use App\Helpers\WorkFlowPermissionHelper;
use App\Http\Controllers\Controller;
use App\Models\DynamicWorkflowModule;
use Illuminate\Http\Request;

class CasteManagementController extends Controller
{
    public function index()
    {
        if (WorkFlowPermissionHelper::canModifyCaste()) {
            // if (Auth::user()->can('modify caste')) {
            $header = 'Caste Modification Information';
            $moduleCode = 'caste_mng_01';
            $mainModule = DynamicWorkflowModule::where('module_code', $moduleCode)->first();
            if (!$mainModule) {
                abort(404, 'Module not found');
            }
            $moduleName = $mainModule->name;
            $mainModuleId = $mainModule->id;
            return view('dynamic-workflow.caste-managment.index', compact('header', 'moduleCode', 'moduleName', 'mainModuleId'));
        }
        $header = 'Oops! You do not have permission to modify caste.';
        return view('CommonRestictedpage.index', compact('header'));
    }
}
