<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoleOfficeTypeMappingsController extends Controller
{
    public function index()
    {
        return view('roleofficeTypemappings.index');
    }
}
