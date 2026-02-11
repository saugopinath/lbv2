<?php

namespace App\Http\Controllers;

use App\Models\Codemaster;
use App\Models\OfficeMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\CheckAuthHelper;
use App\Helpers\WorkFlowPermissionHelper;
use Illuminate\Support\Facades\Crypt;

class WorkFLowController extends Controller
{
    protected $isAuthorized = false;
    // public function __construct()
    // {
    //     if (CheckAuthHelper::isCommonWorkFlow2ndStep()) {
    //         $this->isAuthorized = true;
    //     } else {
    //         redirect()->route('dashboard')
    //             ->with('error', 'Oops! You are not authorized to perform this action.')
    //             ->send();
    //     }
    // }   

    public function index()
    {
        // if (WorkFlowPermissionHelper::canViewLbApplications()) {
            // if (Auth::user()->can('view lb applications')) {
            $button_show = 1;
            return view('WorkFLow.SubmittedList', compact('button_show'));
        // }
        // $header = 'Oops! You do not have permission to view lb applications.';
        // return view('CommonRestictedpage.index', compact('header'));
    }
}