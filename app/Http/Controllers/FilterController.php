<?php

namespace App\Http\Controllers;

use App\Models\Codemaster;
use Illuminate\Http\Request;

class FilterController  extends Controller
{
    public function __construct() {}

    public function index()
    {
        $login_type = 'state_office';
        return view('filter', compact('login_type'));
    }
}
