<?php

namespace App\Http\Controllers;

use App\Models\Scheme;
use App\Models\WorkflowStep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class workflowmanagementController extends Controller
{
    public function index()
    {
        return view('workflowmanagement.index');
    }
    public function createSteps(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'scheme' => 'required|exists:schemes,id',
                'noofSteps' => 'required|integer|min:1',
            ]);
            try {
                DB::transaction(function () use ($request) {
                    $schemeId = $request->scheme;
                    $totalSteps = (int) $request->noofSteps;
                    $parentId = null;
                    for ($i = 1; $i <= $totalSteps; $i++) {
                        $step = new WorkflowStep();
                        $step->scheme_id = $schemeId;
                        $step->rank = $i;
                        $step->label = $request->input('labelName' . $i);
                        $step->parent_id = $parentId;
                        $step->is_first = ($i === 1);
                        $step->is_last = ($i === $totalSteps);
                        $step->save();
                        $parentId = $step->id;
                    }
                });
                return redirect()->route('create-steps')->with('success', 'Workflow created successfully');
            } catch (\Exception $e) {
                return back()->withInput()->with('error', 'Failed to create workflow');
            }
        }
        $schemes = Scheme::whereDoesntHave('workflowSteps')->get();
        return view('workflowmanagement.createsteps', compact('schemes'));
    }
    public function assignWorkflow()
    {
        $schemes = Scheme::has('workflowSteps')->get();
        return view('workflowmanagement.assignworkflows', compact('schemes'));
    }
}
