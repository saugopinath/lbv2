<?php

namespace App\Http\Controllers;

use App\Models\Scheme;
use Illuminate\Http\Request;

class workflowmanagementController extends Controller
{
    public function createSteps(Request $request)
    {
        $schemes = Scheme::all();
        return view('workflowmanagement.index', compact('schemes'));
    }
    public function assignWorkflow()
    {
        dd('ok');
    }
}
