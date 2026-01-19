<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class CreateAssignOtherFormFieldController extends Controller
{
    public function createdynamicformfield(Request $request)
    {
        $data['scheme_id'] = Crypt::decryptString($request->scheme_id);
        $data['tab_code'] = Crypt::decryptString($request->tab_code);
        
        // dd($data);
        $header = 'Create Other from-field Attribute';
        return view('CreateAssignOtherFormField.create_other_fromfields_attribute_index', compact('header','data'));
    }
}
