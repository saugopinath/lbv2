<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class CreateAssignOtherFormFieldController extends Controller
{
    public function createdynamicformfield(Request $request)
    {
        $data = [
            'scheme_id' => null,
            'tab_code'  => null,
        ];

        $header = 'Create Other Form-field Attribute';

        try {
            if ($request->filled('scheme_id')) {
                $data['scheme_id'] = Crypt::decryptString($request->scheme_id);
            }

            if ($request->filled('tab_code')) {
                $data['tab_code'] = Crypt::decryptString($request->tab_code);
            }
        } catch (DecryptException $e) {
            // Just ignore invalid payload and continue opening the same page
            $data['scheme_id'] = null;
            $data['tab_code']  = null;
        }

        return view(
            'CreateAssignOtherFormField.create_other_fromfields_attribute_index',
            compact('header', 'data')
        );
    }
}
