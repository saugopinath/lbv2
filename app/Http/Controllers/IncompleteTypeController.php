<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IncompleteTypeController extends Controller
{
     public function index()
    {
        return view('incomplete_types.index');
    }
}
