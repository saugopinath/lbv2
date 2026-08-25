<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Helpers\CheckAuthHelper;
use App\Helpers\WorkFlowPermissionHelper;
use Illuminate\Http\Request;
use App\Models\User;

class UsersController extends Controller
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
        if (WorkFlowPermissionHelper::canViewUser()) {      
            return view('users.index');
        }
        $header = 'Oops! You do not have permission to view users.';
        return view('CommonRestictedpage.index', compact('header'));
     }
     public function assignPermissionRole(User $user)
    {
    return view('users.assign-permission-role', compact('user'));
    }
}
