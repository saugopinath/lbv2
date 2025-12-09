<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BackFromJBController extends Controller
{
    public function backfromjb() {
        $header = 'Back from JB';
        return view('backfromjb.list', compact('header'));
    }
}
