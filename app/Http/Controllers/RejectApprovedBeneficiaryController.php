<?php

namespace App\Http\Controllers;

use App\Helpers\CheckAuthHelper;
use App\Helpers\WorkFlowPermissionHelper;
use App\Models\AcceptRejectInfo;
use App\Models\BeneficiaryCommonList;
use App\Models\BeneficiaryEnclosure;
use App\Models\BeneficiaryPersonalDetail;
use App\Models\Codemaster;
use App\Models\Scheme;
use App\Models\SchemeAttachedDocMappings;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class RejectApprovedBeneficiaryController extends Controller
{
    protected $doctype;
    public function __construct()
    {
        $this->doctype = [Codemaster::getIdByCode(1635)];
    }
    public function index()
    {
        $user = Auth::user();       

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
        if (WorkFlowPermissionHelper::canViewDetailsToReject()) {
            $header = 'De-Activate Beneficiary Details';
            $application_id = Crypt::decryptString($request->application_id);
            $beneficiary_id = Crypt::decryptString($request->beneficiary_id);
            $scheme_id = Crypt::decryptString($request->scheme_id);
            $schemeName = Scheme::where('id', $scheme_id)->first()->name;
            $reportType = 3;
            $BenDetails = BeneficiaryPersonalDetail::where('application_id', $application_id)->firstOrFail();
            $rejectRevertCause = Codemaster::where('code', 12)->first()->children()->get();
          
            $doctypes = $this->doctype;
            
            return view('RejectApprovedBeneficiaryView.reject_approved_beneficiary_processed', compact('application_id', 'beneficiary_id', 'scheme_id', 'header', 'reportType', 'rejectRevertCause', 'doctypes', 'schemeName'));
        }
        $header = 'Oops! You do not have permission to view users.';
        return view('CommonRestictedpage.index', compact('header'));
    }
    public function deActiveBeneficiary(Request $request)
    {
       
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login first!');
        }

        if (!CheckAuthHelper::isCommonApprover()) {
            return redirect()->route('login')->with('error', 'You are not authorized to perform this action!');
        }
        if (WorkFlowPermissionHelper::canRejectBeneficiary()) {
            $userId = Auth::id();
            $nextLevelRoleId = -100;
            $applicationId = Crypt::decryptString($request->application_id);
            $schemeId = Crypt::decryptString($request->scheme_id);
            $doctype = $this->doctype;           
            $validator = Validator::make($request->all(), [
                'application_id' => 'required|string',
                'beneficiary_id' => 'required|string',
                'reject_reason'  => 'required|integer|exists:codemasters,id',
                'remark'         => 'required|string',
            ], [
                'application_id.required' => 'Invalid application.',
                'beneficiary_id.required' => 'Invalid beneficiary.',
                'reject_reason.required'  => 'Please select a reason.',
                'remark.required'         => 'Please enter a remark.',
            ]);
            $uploadedDocsCount = BeneficiaryEnclosure::where('application_id', $applicationId)->where('scheme_id', $schemeId)
                ->whereIn('document_type', [$doctype])
                ->count();

            $validator->after(function ($validator) use ($uploadedDocsCount) {
                if ($uploadedDocsCount < 1) {
                    $validator->errors()->add('document', 'Please upload the required document.');
                }
            });
            if ($validator->fails()) {              
                return back()
                    ->withErrors($validator)
                    ->withInput();
            }
            // Get validated data
            $validatedData = $validator->validated();
            try {
                $applicationId = Crypt::decryptString($validatedData['application_id']);
            } catch (\Exception $e) {
                return back()->with('error', 'Invalid application id.');
            }

            try {
                $beneficiaryIdFromForm = Crypt::decryptString($validatedData['beneficiary_id']);
            } catch (\Exception $e) {
                $beneficiaryIdFromForm = null;
            }

            $beneficiary = BeneficiaryPersonalDetail::where('application_id', $applicationId)->where('scheme_id', $schemeId)
                ->first();
            if (!$beneficiary) {
                return back()->with('error', 'Beneficiary not found!');
            }
            DB::beginTransaction();
            try {
                $logdetails = new AcceptRejectInfo;
                $logdetails->application_id         = $beneficiary->application_id;
                $logdetails->beneficiary_id         = $beneficiary->beneficiary_id;
                $logdetails->scheme_id              = $beneficiary->scheme_id;
                $logdetails->ip_address             = request()->ip();
                $logdetails->user_id                = $userId;
                $logdetails->browser                = request()->header('User-Agent');
                $logdetails->model_name             = request()->path();
                $logdetails->op_type                = Codemaster::getIdByCode(-1);
                $logdetails->revert_reason_cause_id = $validatedData['reject_reason'];
                $logdetails->revert_reason_remarks  = $validatedData['remark'];
                $logdetails->parent_id              = null;
                $logdetailsSaved = $logdetails->save();

                $updatepersonal = $beneficiary->update([
                    'next_level_role_id' => $nextLevelRoleId,
                    'is_clean' => 10,
                ]);
                if ($logdetailsSaved && $updatepersonal) {
                    DB::commit();
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
        $header = 'Oops! You do not have permission to view users.';
        return view('CommonRestictedpage.index', compact('header'));
    }
}
