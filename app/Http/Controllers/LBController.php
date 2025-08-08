<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LBController extends Controller
{
    public function __construct() {
    }

    public function index()
    {
        return view('lbform.lbform');
    }
}
