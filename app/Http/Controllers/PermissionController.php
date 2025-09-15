<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        // dd('here');
                return view('permissions.permission_index');
    }
}
