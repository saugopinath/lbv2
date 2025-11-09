<?php

namespace App\Http\Controllers;
<<<<<<< HEAD

=======
use Illuminate\Support\Facades\Auth;
use App\Helpers\CheckAuthHelper;
use App\Helpers\WorkFlowPermissionHelper;
>>>>>>> d726694e2ff4cbf8a12d9642a72f953c3c34c7b5
use Illuminate\Http\Request;

class PermissionController extends Controller
{
<<<<<<< HEAD
    public function index()
    {
        // dd('here');
                return view('permissions.permission_index');
=======
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
        // if (Auth::user()->can('view permission')) {
            return view('permissions.permission_index');
        }

        $header = 'Oops! You do not have permission to view permission.';
        return view('CommonRestictedpage.index', compact('header'));
>>>>>>> d726694e2ff4cbf8a12d9642a72f953c3c34c7b5
    }
}
