<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IncompleteTypeController extends Controller
{
    public function index($stage = 'verifier')
    {
        return view('incomplete_types.index', ['stage' => $stage]);
    }
}
