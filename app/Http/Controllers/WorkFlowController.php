<?php

namespace App\Http\Controllers;

use App\Models\Codemaster;
use App\Models\OfficeMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\CheckAuthHelper;
use Illuminate\Support\Facades\Crypt;

class WorkFLowController extends Controller
{
    protected $isAuthorized = false;
    public function __construct()
    {
        if (CheckAuthHelper::isCommmonVerifier() || CheckAuthHelper::isCommonApprover()) {
            $this->isAuthorized = true;
        } else {
            abort(response()->view('CommonRestictedpage.index', [
                'header' => 'Oops! You are not authorized to perform this action.'
            ]));
        }
    }

    public function index()
    {
        if (Auth::user()->can('view lb applications')) {
            $button_show = 1;
            return view('WorkFLow.SubmittedList', compact('button_show'));
        }
        $header = 'Oops! You do not have permission to view lb applications.';
        return view('CommonRestictedpage.index', compact('header'));
    }
}
