<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RolePermisssionManagementController extends Controller
{
   public function index()
    {      
        return view('RolePermissionManagement.index');
    }
}
