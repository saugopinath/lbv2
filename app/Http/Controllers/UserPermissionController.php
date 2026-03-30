<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Helpers\CheckAuthHelper;
use App\Helpers\WorkFlowPermissionHelper;
use Illuminate\Http\Request;

class UserPermissionController extends Controller
{
    protected $isAuthorized = false;
    public function __construct()
    {
        if (CheckAuthHelper::isCommonPrivilegedUser()) {
            $this->isAuthorized = true;
        } else {
             redirect()->route('dashboard')
                ->with('error', 'Oops! You are not authorized to perform this action.')
                ->send();
        }
    }
     public function index()
    {
         if (WorkFlowPermissionHelper::canViewUserPermisson()) {        
            return view('UserPermissions.user_permission_index');
        }

        $header = 'Oops! You do not have permission to view user permission.';
        return view('CommonRestictedpage.index', compact('header'));
    }
}
