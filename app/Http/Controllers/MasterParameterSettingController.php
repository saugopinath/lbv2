<?php

namespace App\Http\Controllers;

use App\Models\SchemeValidationParameterSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;

class MasterParameterSettingController extends Controller
{
    public function index()
    {
        $lgd_session = Session::get('lgd_session');
        if (!$lgd_session) {
            return redirect()->route('login')->with('error', 'Session expired. Please log in again.');
        } else {
            foreach ($lgd_session as $k => $v) {
                $filter[$k] = Crypt::decryptString($v);
            }
        }
        $header='Failed Type Master Parameter Settings';

        return view('MasterParameterSetting.index', compact( 'header'));
    }
    public function edit(request $request){
        // dd('buih');
        // dd($request->all());
        // Get the scheme_id and master_code from the request
        $scheme_id = $request->scheme_id;
        $master_code = $request->master_code;
        // dd($scheme_id, $master_code);

        // Find the parameter setting by scheme_id and master_code
        $parameterSetting = SchemeValidationParameterSetting::where('scheme_id', $scheme_id)
            ->where('master_code', $master_code)
            ->get();
        // dd($parameterSetting);
        if (!$parameterSetting) {
            return redirect()->route('master-parameter-settings.index')->with('error', 'Parameter setting not found.');
        }

        return view('MasterParameterSetting.edit', compact('parameterSetting'));
    }
}

