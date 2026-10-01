<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Formcontroller extends Controller
{
    public function index(Request $request)
    {
        /* OLD CODE PRESERVED FOR BACKWARD COMPATIBILITY:
        $moduleCode = $request->query('module');
        $moduleCode = decrypt($moduleCode);
        abort_unless(
            $moduleCode && \App\Helpers\WorkFlowPermissionHelper::canAccessModule($moduleCode),
            403,
            'You do not have access to this module.'
        );
        */

        $moduleParam = $request->query('module');
        $moduleCode = null;
        if ($moduleParam) {
            try {
                $moduleCode = decrypt($moduleParam);
            } catch (\Exception $e) {
                $moduleCode = null;
            }
        }

        return view('form', compact('moduleCode'));
    }
    public function applicationLists(Request $request)
    {
        /* OLD CODE PRESERVED FOR BACKWARD COMPATIBILITY:
        $moduleCode = $request->query('module');
        $moduleCode = decrypt($moduleCode);
        abort_unless(
            $moduleCode && \App\Helpers\WorkFlowPermissionHelper::canAccessModule($moduleCode),
            403,
            'You do not have access to this module.'
        );
        */

        $moduleParam = $request->query('module');
        $moduleCode = null;
        if ($moduleParam) {
            try {
                $moduleCode = decrypt($moduleParam);
            } catch (\Exception $e) {
                $moduleCode = null;
            }
        }

        return view('applicationlists', compact('moduleCode'));
    }
}
