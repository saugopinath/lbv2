<?php

namespace App\Http\Controllers\DynamicWorkflow;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UpdateMarkBeneficiaryDetailsController extends Controller
{
    public function index()
    {     
        return view('dynamic-workflow.process-workflow-table');
    }
}
