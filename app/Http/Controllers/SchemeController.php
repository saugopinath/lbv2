<?php

namespace App\Http\Controllers;

use App\Helpers\WorkFlowPermissionHelper;
use App\Models\Scheme;
use App\Models\SchemeFinalSubmitCheck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Route;

class SchemeController extends Controller
{
    public function finalSubmitted($stage = null)
    {
        $schemes = '';
        $route = Route::currentRouteName();
        if ($route == 'schemes.final-submitted') {
            $schemes = SchemeFinalSubmitCheck::where('is_final_submitted', true)
                ->whereHas('scheme')
                ->with('scheme')
                ->get()
                ->pluck('scheme')
                ->unique('id')
                ->values();
        } else {
            $schemes = Scheme::all();
        }

        return view('schemesblade.dropdown', compact('schemes', 'stage'));
    }

    public function defineWorkflow()
    {
        $steps = [
            [
                'title' => 'Create Workflow Steps',
                'description' => 'Define the number of steps required in the workflow process.',
                'route' => 'create-steps',
                'step' => 1,
            ],
            [
                'title' => 'Assign Role to Steps',
                'description' => 'Assign specific roles to each workflow step.',
                'route' => 'assign-workflow',
                'step' => 2,
            ],
            [
                'title' => 'Duplicate Check Configuration',
                'description' => 'Set rules to prevent duplicate entries.',
                'route' => 'duplicate-checks',
                'step' => 3,
            ],
            [
                'title' => 'Age Management Configuration',
                'description' => 'Define age validation rules and eligibility.',
                'route' => 'age-management',
                'step' => 4,
            ],
        ];

        return view('workflow.mastercon', compact('steps'));
    }

    public function draftedit(Request $request)
    {
        if (WorkFlowPermissionHelper::canEditDraft()) {
            $app_id = Crypt::decryptString($request->app_id);
            $ben_id = Crypt::decryptString($request->ben_id);
            $scheme_id = Crypt::decryptString($request->scheme_id);
            $schemeName = Scheme::find($scheme_id)->name;

            return view('schemesblade.draftedit', compact('app_id', 'ben_id', 'scheme_id', 'schemeName'));
        }

        $header = 'Oops! You do not have permission to edit draft.';

        return view('CommonRestictedpage.index', compact('header'));
    }

    public function applicationView(Request $request)
    {
        if (WorkFlowPermissionHelper::canEditDraft()) {
            $app_id = Crypt::decryptString($request['id']);
            $scheme_id = Crypt::decryptString($request['scheme_id']);
            $schemeName = Scheme::find($scheme_id)->name;

            return view('schemesblade.applicationview', compact('app_id', 'scheme_id', 'schemeName'));
        }

        $header = 'Oops! You do not have permission to edit draft.';

        return view('CommonRestictedpage.index', compact('header'));
    }

    public function lgd()
    {
        return view('schemesblade.lgd');
    }
}
