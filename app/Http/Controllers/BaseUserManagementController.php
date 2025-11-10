<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BaseUserManagementController extends Controller
{
public function index()
    {
        // return view('UserPermissions.user_permission_index');
        return view('BaseUserManagement.base_user_management_index');
    }
}