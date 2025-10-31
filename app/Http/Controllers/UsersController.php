<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Helpers\CheckAuthHelper;
use Illuminate\Http\Request;

class UsersController extends Controller
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
        if (Auth::user()->can('view users')) {
            return view('users.index');
        }
        $header = 'Oops! You do not have permission to view users.';
        return view('CommonRestictedpage.index', compact('header'));
    }
}
