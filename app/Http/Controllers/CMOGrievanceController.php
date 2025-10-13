<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CMOGrievanceController extends Controller
{
    public function index()
    {
         $login_type = 'state_office';

        return view('cmo_grievances.create',compact('login_type'));
    }

    public function store(Request $request)
    {
        dd($request->all());
    }
}
