<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Formcontroller extends Controller
{
    public function index(Request $request)
    {
        $moduleCode = $request->query('module');
        $moduleCode = decrypt($moduleCode);
        abort_unless(
            $moduleCode && \App\Helpers\WorkFlowPermissionHelper::canAccessModule($moduleCode),
            403,
            'You do not have access to this module.'
        );

        return view('form', compact('moduleCode'));
    }
    public function applicationLists(Request $request)
    {
        $moduleCode = $request->query('module');
        $moduleCode = decrypt($moduleCode);
        abort_unless(
            $moduleCode && \App\Helpers\WorkFlowPermissionHelper::canAccessModule($moduleCode),
            403,
            'You do not have access to this module.'
        );

        return view('applicationlists', compact('moduleCode'));
    }
}
