<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Helpers\CheckAuthHelper;
use App\Helpers\WorkFlowPermissionHelper;
use Illuminate\Http\Request;

class RoleOfficeTypeMappingsController extends Controller
{
    // protected $isAuthorized = false;
    // public function __construct()
    // {
    //     if (CheckAuthHelper::isCommonPrivilegedUser()) {
    //         $this->isAuthorized = true;
    //     } else {
    //         redirect()->route('dashboard')
    //             ->with('error', 'Oops! You are not authorized to perform this action.')
    //             ->send();
    //     }
    // }
    public function index()
    {      
        // if (WorkFlowPermissionHelper::canRoleMapping()) {
        // if (Auth::user()->can('manage role mappings')) {
            return view('roleofficeTypemappings.index');
        // }
        // $header = 'Oops! You do not have permission to manage role mappings.';
        // return view('CommonRestictedpage.index', compact('header'));
    }
}
