<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IncompletPageController extends Controller
{
    public function page($id)
    {
        return view('incomplet.page', compact('id'));
    }
}


