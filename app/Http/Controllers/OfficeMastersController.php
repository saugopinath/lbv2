<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OfficeMastersController extends Controller
{
    public function index()
    {
        return view('officemasters.index');
    }
}
