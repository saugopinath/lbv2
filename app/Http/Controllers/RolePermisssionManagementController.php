<?php

namespace App\Http\Controllers;

use App\Helpers\CheckAuthHelper;
use App\Helpers\WorkFlowPermissionHelper;
use Illuminate\Http\Request;

class RolePermisssionManagementController extends Controller
{
//    public function index()
//     {
//         // return view('UserPermissions.user_permission_index');
//         return view('RolePermissionManagement.index');
//     }


     protected $isAuthorized = false;
    public function __construct()
    {
        if (CheckAuthHelper::isSuperAdmin()) {
            $this->isAuthorized = true;
        } else {
            redirect()->route('dashboard')
                ->with('error', 'Oops! You are not authorized to perform this action.')
                ->send();
        }
    }
    public function index()
    {
        if (WorkFlowPermissionHelper::canRolePermissionManagement()) {
            // if (Auth::user()->can('view offices')) {
            return view('RolePermissionManagement.index');
        }
        $header = 'Oops! You do not have permission to view offices.';
        // return view('RolePermissionManagement.index',compact('header'));
        return view('CommonRestictedpage.index', compact('header'));
    }
}
