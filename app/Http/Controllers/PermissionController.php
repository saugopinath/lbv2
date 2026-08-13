<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Helpers\CheckAuthHelper;
use App\Helpers\WorkFlowPermissionHelper;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
     protected $isAuthorized = false;
    public function __construct()
    {
        if (CheckAuthHelper::isCommonWorkFlow4thStep()) {
            $this->isAuthorized = true;
        } else {
             redirect()->route('dashboard')
                ->with('error', 'Oops! You are not authorized to perform this action.')
                ->send();
        }
    }
     public function index()
    {
        if (WorkFlowPermissionHelper::canViewPermission()) {       
            return view('permissions.permission_index');
        }

        $header = 'Oops! You do not have permission to view permission.';
        return view('CommonRestictedpage.index', compact('header'));
    }
}
