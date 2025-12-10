<?php

namespace App\Http\Controllers;

use App\Models\BackFromJb;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;

class BackFromJBController extends Controller
{
    public function backfromjb()
    {
        $header = 'Back from JB';
        return view('backfromjb.list', compact('header'));
    }

    public function backfromjbactions(Request $request)
    {
        $applicant_details['applicationId'] = Crypt::decryptString($request->id);
        $record = BackFromJb::with([
            'beneficiary.sourceable',
            'beneficiary.sourceable.relationships'
        ])->find($applicant_details['applicationId']);
        $applicant_details['jb_poposed_dob_show'] = Carbon::parse($record->jb_poposed_dob)->format('d-m-Y');
        $applicant_details['jb_poposed_dob'] = $record->jb_poposed_dob;
        $applicant_details['minDOB'] = now()->subYears(60)->format('Y-m-d');
        $applicant_details['maxDOB'] = now()->subYears(25)->format('Y-m-d');
        $applicant_details['dob'] = Carbon::parse($record->beneficiary->sourceable->dob)->format('d-m-Y');
        $applicant_details['email'] =  $record->beneficiary->sourceable->email;
        $applicant_details['name'] = $record->beneficiary->beneficiary_name;
        $applicant_details['mobileNo'] = $record->beneficiary->mobile_no;
        $applicant_details['motherName'] = $record->beneficiary->sourceable->relationships->first()->getFullNameByCode(132);
        $applicant_details['fatherName'] = $record->beneficiary->sourceable->relationships->first()->getFullNameByCode(131);
        return view('backfromjb.applicantDetails',compact('applicant_details'));
    }
}
