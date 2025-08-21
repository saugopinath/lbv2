<?php

namespace App\Http\Controllers;

use App\Models\Scheme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use App\Models\UserRoleSchemeOfficeMapping;

class DashboardController  extends Controller
{
    public function __construct() 
    {

    }

    public function index()
    {
        $user_id = Auth::id();
        // dd($user_id);
        $roleSchemeOfficeMappings = UserRoleSchemeOfficeMapping::with('Office')->where('user_id',$user_id)->first();
        // dd($roleSchemeOfficeMappings);

        $lgd_session=array();
        if(!is_null($roleSchemeOfficeMappings)){
         
        Session::put('lgd_session', $lgd_session);
        if(!is_null($roleSchemeOfficeMappings->scheme_id)){
          $lgd_session['scheme_id']=Crypt::encryptString($roleSchemeOfficeMappings->scheme_id);
        }
        if(!is_null($roleSchemeOfficeMappings->role_id)){
          $lgd_session['role_id']=Crypt::encryptString($roleSchemeOfficeMappings->role_id);
        }
        if(!is_null($roleSchemeOfficeMappings->Office->office_type_id)){
          $lgd_session['office_type_id']=Crypt::encryptString($roleSchemeOfficeMappings->Office->office_type_id);
        }
        if(!is_null($roleSchemeOfficeMappings->Office->state_id)){
          $lgd_session['state_id']=Crypt::encryptString($roleSchemeOfficeMappings->Office->state_id);
        }
        if(!is_null($roleSchemeOfficeMappings->Office->district_id)){
          $lgd_session['district_id']=Crypt::encryptString($roleSchemeOfficeMappings->Office->district_id);
        }
        if(!is_null($roleSchemeOfficeMappings->Office->block_id)){
          $lgd_session['block_id']=Crypt::encryptString($roleSchemeOfficeMappings->Office->block_id);
        }
        if(!is_null($roleSchemeOfficeMappings->Office->subdivision_id)){
          $lgd_session['subdivision_id']=Crypt::encryptString($roleSchemeOfficeMappings->Office->subdivision_id);
        }
        if(!is_null($roleSchemeOfficeMappings->Office->panchayat_id)){
          $lgd_session['panchayat_id']=Crypt::encryptString($roleSchemeOfficeMappings->Office->panchayat_id);
        }
        if(!is_null($roleSchemeOfficeMappings->Office->municipalitiy_id)){
          $lgd_session['municipalitiy_id']=Crypt::encryptString($roleSchemeOfficeMappings->Office->municipalitiy_id);
        }
        if(!is_null($roleSchemeOfficeMappings->Office->ward_id)){
          $lgd_session['ward_id']=Crypt::encryptString($roleSchemeOfficeMappings->Office->ward_id);
        }
      }else{
       $scheme_id=Scheme::where('short_name','LB')->first();
        $lgd_session['scheme_id']=Crypt::encryptString($scheme_id->id);
        $lgd_session['office_type_id']=Crypt::encryptString(151);
        $lgd_session['state_id']=Crypt::encryptString(1);
     
      }
        Session::put('lgd_session', $lgd_session);
        // dd($lgd_session);
        return view('admin.index');
    }

   
}