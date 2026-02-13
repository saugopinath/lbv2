<?php

namespace App\Http\Controllers;

use App\Models\Scheme;
use App\Models\SchemeFinalSubmitCheck;
use Illuminate\Support\Facades\Route;

class SchemeController extends Controller
{
    public function finalSubmitted()
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
        return view('schemesblade.dropdown', compact('schemes'));
    }

    public function masterConfiguration()
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
}
