<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use App\Helpers\CheckAuthHelper;
use Illuminate\Http\Request;

class LBController extends Controller
{
    protected $isAuthorized = false;
    public function __construct()
    {
        if (CheckAuthHelper::isCommonOperator()) {
            $this->isAuthorized = true;
        } else {
            abort(response()->view('CommonRestictedpage.index', [
                'header' => 'Oops! You are not authorized to perform this action.'
            ]));
        }
    }

    /** LB Form Submit Page */
    public function index()
    {
        if (Auth::user()->can('submit lb form')) {
            return view('lbform.lbform');
        }

        $header = 'Oops! You do not have permission to submit lb form.';
        return view('CommonRestictedpage.index', compact('header'));
    }

    /**  Draft List Page */
    public function draftlist()
    {
        if (Auth::user()->can('view draft list')) {
            $button_show = 1;
            return view('lbform.draftlist', compact('button_show'));
        }

        $header = 'Oops! You do not have permission to view draft list.';
        return view('CommonRestictedpage.index', compact('header'));
    }

    /** Draft Edit Page */
    public function draftedit($id)
    {
        if (Auth::user()->can('edit draft')) {
            $id = Crypt::decryptString($id);
            return view('lbform.draftedit', compact('id'));
        }

        $header = 'Oops! You do not have permission to edit draft.';
        return view('CommonRestictedpage.index', compact('header'));
    }
}
