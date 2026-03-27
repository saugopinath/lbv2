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
  public function __construct() {}

  public function index()
  {
    $user_id = Auth::id();
    $mappings = UserRoleSchemeOfficeMapping::with('Office', 'Scheme')->where('user_id', $user_id)->where('is_active', 1)->get();

    $lgd_session = array();

    if ($mappings->isNotEmpty()) {
      // For now, default to the first mapping if no scheme_id in session,
      // or keep the one already in session if it's still valid for this user.
      $currentSchemeId = session('scheme_id');
      $activeMapping = $mappings->firstWhere('scheme_id', $currentSchemeId) ?? $mappings->first();

      if ($activeMapping) {
        $lgd_session['scheme_id'] = Crypt::encryptString($activeMapping->scheme_id);
        $lgd_session['role_id'] = Crypt::encryptString($activeMapping->role_id);
        
        if ($activeMapping->Office) {
          $lgd_session['office_type_id'] = Crypt::encryptString($activeMapping->Office->office_type_id);
          $lgd_session['state_id'] = Crypt::encryptString($activeMapping->Office->state_id);
          $lgd_session['district_id'] = Crypt::encryptString($activeMapping->Office->district_id);
          $lgd_session['block_id'] = Crypt::encryptString($activeMapping->Office->block_id);
          $lgd_session['subdivision_id'] = Crypt::encryptString($activeMapping->Office->subdivision_id);
          $lgd_session['panchayat_id'] = Crypt::encryptString($activeMapping->Office->panchayat_id);
          $lgd_session['municipalitiy_id'] = Crypt::encryptString($activeMapping->Office->municipalitiy_id);
          $lgd_session['ward_id'] = Crypt::encryptString($activeMapping->Office->ward_id);
        }

        Session::put('scheme_id', $activeMapping->scheme_id);
      }
    } else {
      // Fallback for users without mappings (e.g. Super Admin if not mapped)
      $scheme = Scheme::where('short_name', 'LB')->first();
      if ($scheme) {
          $lgd_session['scheme_id'] = Crypt::encryptString($scheme->id);
          Session::put('scheme_id', $scheme->id);
      }
      $lgd_session['office_type_id'] = Crypt::encryptString(151);
      $lgd_session['state_id'] = Crypt::encryptString(1);
    }

    Session::put('lgd_session', $lgd_session);

    $dashBoardVisible = (isset($activeMapping) && $activeMapping->role_id == 1) ? 1 : 0;

    return view('admin.index', compact('dashBoardVisible', 'mappings'));
  }
}
