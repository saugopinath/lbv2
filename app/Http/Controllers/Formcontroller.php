<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Formcontroller extends Controller
{
    public function index()
    {
        return view('form');
    }
    public function applicationLists()
    {
        return view('applicationlists');
    }
}
