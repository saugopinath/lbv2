<?php

namespace App\Http\Controllers;

use App\Models\Scheme;
use App\Models\WorkflowStep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class workflowmanagementController extends Controller
{
    public function createSteps(Request $request)
    {
        if ($request->isMethod('post')) {
            DB::transaction(function () use ($request) {
                $schemeId   = $request->scheme;
                $totalSteps = (int) $request->noofSteps;
                $parentId = null;
                for ($i = 1; $i <= $totalSteps; $i++) {
                    $step = new WorkflowStep();
                    $step->scheme_id = $schemeId;
                    $step->rank      = $i;
                    $step->label     = $request->input('labelName' . $i);
                    $step->parent_id = $parentId;
                    $step->is_first  = ($i === 1);
                    $step->is_last   = ($i === $totalSteps);
                    $step->save();
                    $parentId = $step->id;
                }
            });
        }
        $schemes = Scheme::all();
        return view('workflowmanagement.index', compact('schemes'));
    }
    public function assignWorkflow()
    {
        dd('ok');
    }
}
