<?php

namespace App\Http\Controllers;

use App\Helpers\CheckAuthHelper;
use App\Helpers\WorkFlowPermissionHelper;
use App\Models\BackFromJb;
use App\Models\Codemaster;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BackFromJBController extends Controller
{
    protected $minDOB;

    protected $maxDOB;
    protected $isAuthorized = false;
    public function __construct()
    {
        if (CheckAuthHelper::isCommonWorkFlow2ndStep()) {
            $this->isAuthorized = true;
        } else {
            redirect()->route('dashboard')
                ->with('error', 'Oops! You are not authorized to perform this action.')
                ->send();
        }
        $this->minDOB = now()->subYears(60)->format('Y-m-d');
        $this->maxDOB = now()->subYears(25)->format('Y-m-d');
    }

    public function backfromjb()
    {
        if (WorkFlowPermissionHelper::canBackFromJb()) {
            $header = 'Back from JB';

            return view('backfromjb.list', compact('header'));
        }
        $header = 'Oops! You do not have permission to view users.';

        return view('CommonRestictedpage.index', compact('header'));
    }

    public function backfromjbactions(Request $request)
    {
        if ($request->isMethod('post')) {
            $messages = [
                'new_dob.*' => "Date of birth must be between {$this->minDOB} and {$this->maxDOB}.",
            ];
            $validator = Validator::make(
                $request->all(),
                [
                    'action' => 'required|in:verify_and_forward_to_approver,approve',
                ],
                $messages
            );
            $validator->sometimes(
                'new_dob',
                "required|date|after_or_equal:{$this->minDOB}|before_or_equal:{$this->maxDOB}",
                fn($input) => $input->action === 'verify_and_forward_to_approver'
            );
            $validator->validate();
            DB::beginTransaction();
            try {
                $app_id = Crypt::decryptString($request->id);
                $new_dob = $request->new_dob;
                $action = $request->action;
                $record = BackFromJb::with([
                    'beneficiary',
                ])->find($app_id);
                $msg = '';
                if ($action == 'verify_and_forward_to_approver' && CheckAuthHelper::isCommmonVerifier()) {
                    $record->new_dob = $new_dob;
                    $record->next_level_role_id = Codemaster::getIdByCode(4402);
                    $msg = 'The request successfully verified!';
                } elseif ($action == 'approve' && CheckAuthHelper::isCommonApprover()) {
                    $record->next_level_role_id = Codemaster::getIdByCode(4403);
                    $msg = 'The request successfully approved!';
                    $record->beneficiary->dob = $record->new_dob;
                    $record->beneficiary->save();
                }
                $record->save();
                DB::commit();
                session()->flash('success', $msg);

                return redirect()->route('backfromjb');
            } catch (\Exception $e) {
                DB::rollBack();
                session()->flash('error', 'Something went wrong! Please try again.');

                return redirect()->back();
            }
        }
        $applicant_details['applicationId'] = Crypt::decryptString($request->id);
        $record = BackFromJb::with([
            'beneficiary',
        ])->find($applicant_details['applicationId']);
        $applicant_details['jb_poposed_dob_show'] = Carbon::parse($record->jb_poposed_dob)->format('d-m-Y');
        $applicant_details['new_dob'] = Carbon::parse($record->new_dob)->format('d-m-Y');
        $applicant_details['jb_poposed_dob'] = $record->jb_poposed_dob;
        $applicant_details['minDOB'] = $this->minDOB;
        $applicant_details['maxDOB'] = $this->maxDOB;
        $applicant_details['dob'] = Carbon::parse($record->beneficiary->dob)->format('d-m-Y');
        $applicant_details['email'] = $record->beneficiary->email;
        $applicant_details['name'] = $record->beneficiary->beneficiary_name;
        $applicant_details['mobileNo'] = $record->beneficiary->other_details['mobile_no'];
        $applicant_details['motherName'] = $record->beneficiary->ben_mother_name;
        $applicant_details['fatherName'] = $record->beneficiary->ben_father_name;
        $role = '';
        $btnAction = '';
        if (CheckAuthHelper::isCommmonVerifier() && WorkFlowPermissionHelper::canBackFromJbVerifierButton()) {
            $role = 'verifier';
            $btnAction = 'verify_and_forward_to_approver';
            $btnActionText = 'Verify and Forward to Approver';
        } elseif (CheckAuthHelper::isCommonApprover() && WorkFlowPermissionHelper::canBackFromJbApproverButton()) {
            $role = 'approver';
            $btnAction = 'approve';
            $btnActionText = 'Approve';
        }

        return view('backfromjb.applicantDetails', compact('applicant_details', 'role', 'btnAction', 'btnActionText'));
    }
}
