<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserDutyManagementController extends Controller
{
    public function index()
    {
        return view('userdutymanagement.index');
    }   
}
