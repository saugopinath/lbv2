<?php

namespace App\Http\Controllers;

use App\Helpers\CheckAuthHelper;
use App\Models\AcceptRejectInfo;
use App\Models\BeneficiaryCommonList;
use App\Models\Codemaster;
use App\Models\SchemeAttachedDocMappings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class RejectApprovedBeneficiaryController extends Controller
{
    public function index()
    {
        // $user = auth()->user();
        // $user->hasRole('Operator');

        // dd('caste modification info');
        // if ($user->hasRole('Operator')) {}

        if (CheckAuthHelper::isApprover()) {
            $header = 'Reject Approved Beneficiary Information';
            return view('RejectApprovedBeneficiaryView.reject_approved_beneficiary_index', compact('header'));
        } else {
            $header = 'Opps! you are not able to perform any action';
            return view('CommonRestictedpage\index', compact('header'));
        }
    }
    public function editview(Request $request)
    {
        $header = 'De-Activate Beneficiary Details';
        $application_id = Crypt::decryptString($request->application_id);
        $beneficiary_id = Crypt::decryptString($request->beneficiary_id);
        $reportType = 3;
        $BenDetails = BeneficiaryCommonList::where('sourceable_id', $application_id)->with('sourceable')->firstOrFail();
        $rejectRevertCause = Codemaster::where('code', 12)->first()->children()->get();
        // $doctypes=SchemeAttachedDocMappings::with('codemaster')->get();
        $doctypes = Codemaster::where('code', 16)->first()->children()->get();
        // dd($doctypes);
        return view('RejectApprovedBeneficiaryView.reject_approved_beneficiary_processed', compact('application_id', 'header', 'reportType', 'rejectRevertCause', 'doctypes'));
    }
    public function deActiveBeneficiary(Request $request)
    {
        // dd('ok');
        // // dd($request->all());
        // if (!Auth::check()) {
        //     dd('not login');
        //     return redirect()->route('login')->with('error', 'Please login first!');
        // } else if (!CheckAuthHelper::isCommonApprover()) {
        //     dd('not approver');
        //     return redirect()->route('login')->with('error', 'You are not authorized to perform this action!');
        // } else {
        //     $userId = Auth::user()->id;
        //     $next_level_role_id = Codemaster::getIdByCode(-1);
        //     // dd($next_level_role_id);
        //     // dd('login and approver');
        //     $request->validate([
        //         'application_id' => 'required|string',
        //         'reject_reason' => 'required|integer|exists:codemasters,id',
        //         'remark' => 'required',
        //         // 'doctype' => 'required|integer|exists:codemasters,id',

        //     ], [
        //         // application_id
        //         'application_id.required' => 'Invalid application.',
        //         // reject_reason
        //         'reject_reason.required' => 'Please select a caste.',
        //         // remark
        //         'remark.required' => 'Please enter a remark.',
        //         // doctype
        //         // 'doctype.required' => 'Please select a document type.',

        //     ]);
        //     $application_id = Crypt::decryptString($request->application_id);
        //     // dd($application_id);


        //     $beneficiary = BeneficiaryCommonList::where('sourceable_id', $application_id)->with('sourceable')->firstOrFail();
        //     // dd($beneficiary);
        //     if (!$beneficiary) {
        //         return back()->with('error', 'Beneficiary not found!');
        //     }
        //     DB::beginTransaction();
        //     try{
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login first!');
        }

        if (!CheckAuthHelper::isCommonApprover()) {
            return redirect()->route('login')->with('error', 'You are not authorized to perform this action!');
        }

        $userId = Auth::id();
        $nextLevelRoleId = Codemaster::getIdByCode(-1);

        // Validate request — make sure these names match your form inputs.
        // If your select is named 'cause' in the form, change 'reject_reason' to 'cause'
        $request->validate([
            'application_id' => 'required|string',
            'reject_reason'  => 'required|integer|exists:codemasters,id',
            'remark'         => 'required|string',
        ], [
            'application_id.required' => 'Invalid application.',
            'reject_reason.required'  => 'Please select a reason.',
            'remark.required'         => 'Please enter a remark.',
        ]);
        try {
            $applicationId = Crypt::decryptString($request->application_id);
        } catch (\Exception $e) {
            return back()->with('error', 'Invalid application id.');
        }
        $beneficiary = BeneficiaryCommonList::where('sourceable_id', $applicationId)
            ->with('sourceable')
            ->first();
        if (!$beneficiary || !$beneficiary->sourceable) {
            return back()->with('error', 'Beneficiary not found!');
        }
        DB::beginTransaction();
        try {
            $logdetails = new AcceptRejectInfo;
            $logdetails->application_id         = $beneficiary->sourceable_id;
            $logdetails->beneficiary_id         = $beneficiary->beneficiary_id;
            $logdetails->ip_address             = request()->ip();
            $logdetails->user_id                = $userId;
            $logdetails->browser                = request()->header('User-Agent');
            $logdetails->model_name             = request()->path();
            $logdetails->op_type                = Codemaster::getIdByCode(-1);
            $logdetails->revert_reason_cause_id = $request->reject_reason;
            $logdetails->revert_reason_remarks  = $request->remark;
            $logdetails->parent_id              = null;
            // dd($logdetails);
            $logdetailsSaved = $logdetails->save();

            $updatepersonal = $beneficiary->sourceable->update(
                [
                    'next_level_role_id' => $nextLevelRoleId,
                ]
            );
            // dump($updatepersonal);
            // dd($logdetailsSaved);

            if ($logdetailsSaved && $updatepersonal) {
                // $beneficiary->sourceable->update();
                DB::commit();

                session()->flash('success', "Beneficiary De-Activated Successfully!");
                return redirect()->route('reject-approved-beneficiary')->with('success', 'Beneficiary De-Activated Successfully!');
            } else {
                DB::rollBack();
                return redirect()->route('reject-approved-beneficiary')->with('error', 'Something went wrong!');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Something went wrong!');
        }
    }
}
