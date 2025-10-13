<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserPermissionController extends Controller
{
    public function index()
    {
        return view('UserPermissions.user_permission_index');
    }
}
