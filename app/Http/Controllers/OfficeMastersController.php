<?php

namespace App\Http\Controllers;

use App\Helpers\CheckAuthHelper;
use App\Helpers\WorkFlowPermissionHelper;
use Illuminate\Support\Facades\Auth;

class OfficeMastersController extends Controller
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
        if (WorkFlowPermissionHelper::canViewOffices()) {
            // if (Auth::user()->can('view offices')) {
            return view('officemasters.index');
        }
        $header = 'Oops! You do not have permission to view offices.';

        return view('CommonRestictedpage.index', compact('header'));
    }
}
