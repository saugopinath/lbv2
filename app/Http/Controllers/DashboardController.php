<?php

namespace App\Http\Controllers;

use App\Models\Scheme;
use App\Models\UserRoleSchemeOfficeMapping;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function __construct() {}
    public function index()
    {
        $user_id = Auth::id();
        $allMappings = UserRoleSchemeOfficeMapping::with('Office')->where('user_id', $user_id)->get();
        $roleSchemeOfficeMappings = $allMappings->first();
        $scheme_ids = [];

        foreach ($allMappings as $mapping) {
            if (!is_null($mapping->scheme_id)) {
                $scheme_ids[] = Crypt::encryptString($mapping->scheme_id);
            }
        }
        Session::put('scheme_ids', $scheme_ids);

        $lgd_session = [];

        if (!is_null($roleSchemeOfficeMappings)) {

            if (! empty($scheme_ids)) {
                $lgd_session['scheme_id'] = $scheme_ids;
            }

            if (!is_null($roleSchemeOfficeMappings->role_id)) {
                $lgd_session['role_id'] = Crypt::encryptString($roleSchemeOfficeMappings->role_id);
            }

            if (!is_null($roleSchemeOfficeMappings->Office)) {

                $office = $roleSchemeOfficeMappings->Office;

                if (!is_null($office->office_type_id)) {
                    $lgd_session['office_type_id'] = Crypt::encryptString($office->office_type_id);
                }

                if (!is_null($office->state_id)) {
                    $lgd_session['state_id'] = Crypt::encryptString($office->state_id);
                }

                if (!is_null($office->district_id)) {
                    $lgd_session['district_id'] = Crypt::encryptString($office->district_id);
                }

                if (!is_null($office->block_id)) {
                    $lgd_session['block_id'] = Crypt::encryptString($office->block_id);
                }

                if (!is_null($office->subdivision_id)) {
                    $lgd_session['subdivision_id'] = Crypt::encryptString($office->subdivision_id);
                }

                if (!is_null($office->panchayat_id)) {
                    $lgd_session['panchayat_id'] = Crypt::encryptString($office->panchayat_id);
                }

                if (!is_null($office->municipalitiy_id)) {
                    $lgd_session['municipalitiy_id'] = Crypt::encryptString($office->municipalitiy_id);
                }

                if (!is_null($office->ward_id)) {
                    $lgd_session['ward_id'] = Crypt::encryptString($office->ward_id);
                }
            }

        } else {

            $scheme = Scheme::where('short_name', 'LB')->first();

            $lgd_session['scheme_id'] = [Crypt::encryptString($scheme->id)];

            $lgd_session['office_type_id'] = Crypt::encryptString(151);

            $lgd_session['state_id'] = Crypt::encryptString(1);
        }

        Session::put('lgd_session', $lgd_session);

        $roleID = $roleSchemeOfficeMappings->role_id ?? 0;

        $dashBoardVisible = ($roleID == 1) ? 1 : 0;

        return view('admin.index', compact('dashBoardVisible'));
    }
}
