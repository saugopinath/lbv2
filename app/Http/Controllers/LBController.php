<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use App\Helpers\CheckAuthHelper;
use App\Helpers\WorkFlowPermissionHelper;
use App\Models\FromFieldAttribute;
use App\Models\Scheme;
use Illuminate\Http\Request;

class LBController extends Controller
{
    protected $isAuthorized = false;
    public function __construct()
    {
        if (CheckAuthHelper::isCommonOperator()) {
            $this->isAuthorized = true;
        } else {
            redirect()->route('dashboard')
                ->with('error', 'Oops! You are not authorized to perform this action.')
                ->send();
        }
    }

    // public function selectScheme()
    // {
    //     if (WorkFlowPermissionHelper::canEntry()) {
    //         // if (Auth::user()->can('submit lb form')) {
    //         $scheme_ids = Scheme::all();
    //         // $isOtherTab= Scheme::where('scheme_id', $scheme_id)->first()->is_other_tab;
    //         return view('lbform.selectScheme',compact('scheme_ids'));
    //     }

    //     $header = 'Oops! You do not have permission to submit lb form.';
    //     return view('CommonRestictedpage.index', compact('header'));
    // }

    /** LB Form Submit Page */
    public function index(Request $request)
    {
        if (WorkFlowPermissionHelper::canEntry()) {
            // if (Auth::user()->can('submit lb form')) {
            $scheme_id = 21;
            // $scheme_id = $request->scheme_id;
            $isOtherTab = FromFieldAttribute::where('scheme_id', $scheme_id)
                ->where('is_active', true)
                ->exists() ? 1 : 0;
            // Debug
            // dd($isOtherTab); // 0 or 1
            return view('lbform.lbform',compact('isOtherTab'));
        }

        $header = 'Oops! You do not have permission to submit lb form.';
        return view('CommonRestictedpage.index', compact('header'));
    }

    /**  Draft List Page */
    public function draftlist()
    {
        if (WorkFlowPermissionHelper::canDraftList()) {
            // if (Auth::user()->can('view draft list')) {
            $button_show = 1;
            return view('lbform.draftlist', compact('button_show'));
        }

        $header = 'Oops! You do not have permission to view draft list.';
        return view('CommonRestictedpage.index', compact('header'));
    }

    /** Draft Edit Page */
    public function draftedit($id)
    {
        if (WorkFlowPermissionHelper::canEditDraft()) {
            // if (Auth::user()->can('edit draft')) {
            $scheme_id = 20;
            $id = Crypt::decryptString($id);
            $isOtherTab = FromFieldAttribute::where('scheme_id', $scheme_id)
                ->where('is_active', true)
                ->exists() ? 1 : 0;
            return view('lbform.draftedit', compact('id','isOtherTab'));
        }

        $header = 'Oops! You do not have permission to edit draft.';
        return view('CommonRestictedpage.index', compact('header'));
    }
}
