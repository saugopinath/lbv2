<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DynamicFormController extends Controller
{
    public function show(Request $request)
    {
        $scheme_id = 20;
        $applicationId =10002;

        return view('dynamicForm.page', compact('scheme_id','applicationId'));
    }
}
