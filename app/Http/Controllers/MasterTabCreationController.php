<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MasterTabCreationController extends Controller
{
    public function index()
    {
        $header = 'Master Tab Creation';
        return view('MasterTab.Creation', compact('header'));

    }
}
