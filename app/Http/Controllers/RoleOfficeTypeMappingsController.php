<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Helpers\CheckAuthHelper;
use Illuminate\Http\Request;

class RoleOfficeTypeMappingsController extends Controller
{
    protected $isAuthorized = false;
    public function __construct()
    {
        if (CheckAuthHelper::isSuperAdmin() || CheckAuthHelper::isCommmonVerifier() || CheckAuthHelper::isCommonApprover() || CheckAuthHelper::isCommonHOD()) {
            $this->isAuthorized = true;
        } else {
            abort(response()->view('CommonRestictedpage.index', [
                'header' => 'Oops! You are not authorized to perform this action.'
            ]));
        }
    }
    public function index()
    {
        if (Auth::user()->can('manage role mappings')) {
            return view('roleofficeTypemappings.index');
        }
        $header = 'Oops! You do not have permission to manage role mappings.';
        return view('CommonRestictedpage.index', compact('header'));
    }
}
