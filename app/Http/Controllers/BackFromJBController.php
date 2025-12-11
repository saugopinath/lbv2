<?php

namespace App\Http\Controllers;

use App\Models\BackFromJb;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use App\Helpers\CheckAuthHelper;
use App\Models\Codemaster;

class BackFromJBController extends Controller
{
    public function backfromjb()
    {
        $header = 'Back from JB';
        return view('backfromjb.list', compact('header'));
    }

    public function backfromjbactions(Request $request)
    {
        if ($request->isMethod('post')) {
            $app_id =  Crypt::decryptString($request->id);
            $new_dob   = $request->new_dob;
            $action = $request->action;
            $record = BackFromJb::with([
                'beneficiary.sourceable'
            ])->find($app_id);
            if ($action == 'verify_and_forward_to_approver') {
                $record->jb_poposed_dob = $new_dob;
                $record->next_level_role_id = Codemaster::getIdByCode(4402);
            } elseif ($action == 'approve') {
                $record->next_level_role_id = Codemaster::getIdByCode(4403);
                $record->beneficiary->sourceable->dob = $record->jb_poposed_dob;
                $record->beneficiary->sourceable->save();
            }
            $record->save();
        }
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
        $role = '';
        $btnAction = '';
        if (CheckAuthHelper::isCommmonVerifier()) {
            $role = 'verifier';
            $btnAction = 'verify_and_forward_to_approver';
            $btnActionText = 'Verify and Forward to Approver';
        } elseif (CheckAuthHelper::isCommonApprover()) {
            $role = 'approver';
            $btnAction = 'approve';
            $btnActionText = 'Approve';
        }
        return view('backfromjb.applicantDetails', compact('applicant_details', 'role', 'btnAction', 'btnActionText'));
    }
}
