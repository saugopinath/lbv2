<?php

namespace App\Http\Controllers;

use App\Models\BeneficiaryApprovedList;
use App\Models\BeneficiaryCommonList;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;

class BeneficiaryApprovedListController extends Controller
{
    // public function index()
    // {
    //     //user address wise approved list
    //     $lgd_session = Session::get('lgd_session');
    //     // dd($lgd_session);
    //     if (!$lgd_session) {
    //         return redirect()->route('login')->with('error', 'Session expired. Please log in again.');
    //     } else {
    //         // if (isset($lgd_session['scheme_id'])) {
    //         //     $filter['scheme_id'] = Crypt::decryptString($lgd_session['scheme_id']);
    //         // } else {
    //         //     $filter['scheme_id'] = null;
    //         // }
    //         // if (isset($lgd_session['role_id'])) {
    //         //     $filter['role_id'] = Crypt::decryptString($lgd_session['role_id']);
    //         // } else {
    //         //     $filter['role_id'] = null;
    //         // }
    //         // if (isset($lgd_session['office_type_id'])) {
    //         //     $filter['office_type_id'] = Crypt::decryptString($lgd_session['office_type_id']);
    //         // } else {
    //         //     $filter['office_type_id'] = null;
    //         // }
    //         // if (isset($lgd_session['state_id'])) {
    //         //     $filter['state_id'] = Crypt::decryptString($lgd_session['state_id']);
    //         // } else {
    //         //     $filter['state_id'] = null;
    //         // }
    //         // if (isset($lgd_session['district_id'])) {
    //         //     $filter['district_id'] = Crypt::decryptString($lgd_session['district_id']);
    //         // } else {
    //         //     $filter['district_id'] = null;
    //         // }
    //         // if (isset($lgd_session['block_id'])) {
    //         //     $filter['block_id'] = Crypt::decryptString($lgd_session['block_id']);
    //         // } else {
    //         //     $filter['block_id'] = null;
    //         // }
    //         // if (isset($lgd_session['subdivision_id'])) {
    //         //     $filter['subdivision_id'] = Crypt::decryptString($lgd_session['subdivision_id']);
    //         // } else {
    //         //     $filter['subdivision_id'] = null;
    //         // }
    //         foreach ($lgd_session as $k => $v) {
    //             $filter[$k] = Crypt::decryptString($v);
    //         }
    //     }
    //     // dd($lgd_session);
    //     // dd($filter['office_type_id']);
    //     $header = 'User Address wise Approved List';
    //     $condition = [];
    //     if ($filter['office_type_id'] == 151) {
    //         $condition[] = '';
    //     } elseif ($filter['office_type_id'] == 152) {
    //         // $condition['district_id'] = 306;
    //         $condition['district_id'] = $filter['district_id'];
            
    //     } elseif ($filter['office_type_id'] == 153) {
    //         $condition['district_id'] = $filter['district_id'];
    //         $condition['block_id'] = $filter['block_id'];
    //     } elseif ($filter['office_type_id'] == 154) {
    //         $condition['district_id'] = $filter['district_id'];
    //         $condition['subdivision_id'] = $filter['subdivision_id'];
    //     }
    //     // dd($condition);
    //     $lists = BeneficiaryCommonList::with('sourceable.bank')->where($condition)->get();
    //     // dd($lists);
    //     return view('approved_lists.index', compact('lists','header'));
    // }
    
    public function beneficiaryContactwiseList()
    {
        // beneficiary contact-wise list
         $lgd_session = Session::get('lgd_session');
        // dd($lgd_session);
        if (!$lgd_session) {
            return redirect()->route('login')->with('error', 'Session expired. Please log in again.');
        } else {
            foreach ($lgd_session as $k => $v) {
                $filter[$k] = Crypt::decryptString($v);
            }
        }
        $login_type = $filter['office_type_id'];
        // $lists = [];
        $button_show = 1;

        // $header = 'Beneficiary Contact Wise Approved List';
        return view('UserAddresswiselist.approved_list', compact( 'login_type', 'button_show'));
    }
}
