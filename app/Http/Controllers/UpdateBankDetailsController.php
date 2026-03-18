<?php

namespace App\Http\Controllers;

use App\Models\BeneficiaryPersonalDetail;
use App\Models\Codemaster;
use Illuminate\Http\Request;
use App\Helpers\ChechDupHelper;
use App\Helpers\CheckAuthHelper;
use App\Models\AcceptRejectInfo;
use Illuminate\Support\Facades\DB;
use App\Models\BeneficiaryEnclosure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class UpdateBankDetailsController extends Controller
{
    protected $doctype;
    public function index()
    {
        if (CheckAuthHelper::isCommonApprover()) {
            $header = 'Update Bank Details For Approved Beneficiary';
            return view('UpdateBankDetailsView.bank_deatils_index', compact('header'));
        } else {
            $header = 'Opps! you are not able to perform any action';
            return view('CommonRestictedpage.index', compact('header'));
        }
    }
    public function updateBeneficiaryBank($type, Request $request)
    {
        $header = 'Caste Modification Details';
        $doctype = $this->doctype;
        $scheme_id = Crypt::decryptString($request->scheme_id);
        $application_id = Crypt::decryptString($request->application_id);
        $beneficiary_id = Crypt::decryptString($request->beneficiary_id);
        $query = BeneficiaryPersonalDetail::query()
            ->with(['contact', 'banks'])
            ->where('application_id', $application_id)
            ->first();

        $mobileNumber = $query?->contact?->mobile_no ?? '';

        if ($type === 'bank') {
            return view('UpdateBankDetailsView.bank_deatils_update', compact('application_id', 'scheme_id'));
        } elseif ($type === 'mobile') {
            return view('UpdateBankDetailsView.mobile_deatils_update', compact('application_id', 'mobileNumber', 'scheme_id'));
        }
    }
    public function updateMobile(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login first!');
        }

        $validated = $request->validate([
            'application_id' => 'required|string',
            'scheme_id' => 'required|string',
            'mobile' => ['required', 'digits:10'],
            'revert_reason_remarks' => ['required', 'string', 'max:255'],
        ]);

        $application_id = Crypt::decryptString($request->application_id);
        $scheme_id = Crypt::decryptString($request->scheme_id);

        $mobileData = $request->mobile;
        $reasonRemarks = $request->revert_reason_remarks;

        $duplicateCheck = $this->checkduplicate($request);
        if ($duplicateCheck !== true) {
            return back()->withErrors(['duplicate_check' => $duplicateCheck])->withInput();
        }

        try {
            DB::beginTransaction();

            $beneficiary = BeneficiaryPersonalDetail::with('contact')
                ->where('application_id', $application_id)
                ->where('scheme_id', $scheme_id)
                ->first();

            if (!$beneficiary) {
                return back()->with('error', 'Beneficiary not found!');
            }

            $otherDetails = $beneficiary->other_details ?? [];
            $otherDetails['mobile_no'] = $mobileData;

            $beneficiary->updateOrCreate(
                ['application_id' => $application_id],
                [
                    'beneficiary_id' => $beneficiary->beneficiary_id,
                    'other_details' => $otherDetails,
                    'updated_by' => Auth::id(),
                ]
            );

            $AcceptRejectInfo = new AcceptRejectInfo;
            $AcceptRejectInfo->application_id = $application_id;
            $AcceptRejectInfo->beneficiary_id = $beneficiary->beneficiary_id;
            $AcceptRejectInfo->scheme_id = $scheme_id;
            $AcceptRejectInfo->ip_address = request()->ip();
            $AcceptRejectInfo->user_id = Auth::id();
            $AcceptRejectInfo->browser = request()->header('User-Agent');
            $AcceptRejectInfo->model_name = null;
            $AcceptRejectInfo->op_type = Codemaster::getIdByCode(0);
            $AcceptRejectInfo->revert_reason_cause_id = null;
            $AcceptRejectInfo->revert_reason_remarks = $reasonRemarks;
            $AcceptRejectInfo->parent_id = AcceptRejectInfo::where('application_id', $application_id)
                ->latest('id')
                ->value('id') ?? null;
            $AcceptRejectInfo->save();

            DB::commit();

            return redirect()->route('bankUpdate')
                ->with('success', "Mobile updated successfully for Application ID: {$application_id}");

        } catch (\Exception $e) {         
            DB::rollBack();
            return back()->with('error', 'Something went wrong!');
        }
    }
    public function updateBank(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login first!');
        }

        $application_id = Crypt::decryptString($request->application_id);
        $scheme_id = Crypt::decryptString($request->scheme_id);

        $confirmAccData = $request->confirmbankaccountnumber;
        $ifscode = strtoupper($request->ifscode);
        $reasonRemarks = $request->revert_reason_remarks;

        $uploadedDocsCount = BeneficiaryEnclosure::where('application_id', $application_id)
            ->whereIn('document_type', [111])
            ->count();

        $validated = $request->validate([
            'application_id' => 'required|string',
            'bank_account_number' => 'required',
            'confirmbankaccountnumber' => 'required|same:bank_account_number',
            'ifscode' => 'required|regex:/^[A-Z]{4}0[A-Z0-9]{6}$/i',
            'revert_reason_remarks' => 'required|string|max:255',
        ], [
            'bank_account_number.required' => 'Bank Account Number is required.',
            'confirmbankaccountnumber.required' => 'Confirm Bank Account Number is required.',
            'confirmbankaccountnumber.same' => 'Bank Account Number and Confirm Bank Account Number do not match.',
            'ifscode.required' => 'IFSC Code is required.',
            'ifscode.regex' => 'Invalid IFSC format.',
            'revert_reason_remarks.required' => 'Remarks field is required.',
        ]);

        if ($uploadedDocsCount < 1) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'document_type' => ['Please upload the required document.'],
            ]);
        }

        $duplicateCheck = $this->checkduplicate($request);
        if ($duplicateCheck !== true) {
            return back()->withErrors(['duplicate_check' => $duplicateCheck])->withInput();
        }

        try {
            DB::beginTransaction();

            $beneficiary = BeneficiaryPersonalDetail::where('application_id', $application_id)
                ->where('scheme_id', $scheme_id)
                ->with('banks')
                ->first();

            if (!$beneficiary) {
                return back()->with('error', 'Beneficiary not found!');
            }

            $beneficiary->banks()->updateOrCreate(
                ['application_id' => $application_id],
                [
                    'scheme_id' => $scheme_id,
                    'beneficiary_id' => $beneficiary->beneficiary_id,
                    'bankaccountnumber' => $confirmAccData,
                    'ifscode' => $ifscode,                   
                ]
            );

            $AcceptRejectInfo = new AcceptRejectInfo();
            $AcceptRejectInfo->application_id = $application_id;
            $AcceptRejectInfo->beneficiary_id = $beneficiary->beneficiary_id;
            $AcceptRejectInfo->scheme_id = $scheme_id;
            $AcceptRejectInfo->ip_address = request()->ip();
            $AcceptRejectInfo->user_id = Auth::id();
            $AcceptRejectInfo->browser = request()->header('User-Agent');
            $AcceptRejectInfo->model_name = null;
            $AcceptRejectInfo->op_type = Codemaster::getIdByCode(0);
            $AcceptRejectInfo->revert_reason_cause_id = null;
            $AcceptRejectInfo->revert_reason_remarks = $reasonRemarks;
            $AcceptRejectInfo->parent_id = AcceptRejectInfo::where('application_id', $application_id)
                ->latest('id')
                ->value('id') ?? null;
            $AcceptRejectInfo->save();

            DB::commit();

            return redirect()->route('bankUpdate')
                ->with('success', "Bank details updated successfully for Application ID: {$application_id}");

        } catch (\Exception $e) {  
            dd($e);       
            DB::rollBack();
            return back()->with('error', 'Something went wrong! Bank details not updated.');
        }
    }


    public function checkduplicate(Request $request)
    {
        $scheme_id = Crypt::decryptString($request->scheme_id);
        $mobileData = $request->mobile;
        $confirmAccData = $request->confirmbankaccountnumber;

        if (!empty($confirmAccData)) {
            $bankCheck = ChechDupHelper::checkBankMobileDuplicate('bank', $confirmAccData, $scheme_id);
            if ($bankCheck !== true) {
                return $bankCheck;
            }
        }

        if (!empty($mobileData)) {
            $mobileCheck = ChechDupHelper::checkBankMobileDuplicate('mobile', $mobileData, $scheme_id);
            if ($mobileCheck !== true) {
                return $mobileCheck;
            }
        }

        return true;
    }
}
