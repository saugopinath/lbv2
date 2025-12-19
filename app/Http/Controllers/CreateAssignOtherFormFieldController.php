<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CreateAssignOtherFormFieldController extends Controller
{
    public function createdynamicformfield(Request $request)
    {
        // dd('fniji');
        $header = 'Create Other from-field Attribute';
        return view('CreateAssignOtherFormField.create_other_fromfields_attribute_index', compact('header'));
    }
}
