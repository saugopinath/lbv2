<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;

class ValidationfailedSettingController extends Controller
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
        $login_type = $filter['office_type_id'];

        return view('ValidationFailedSetting.index', compact( 'login_type'));
    }
}

