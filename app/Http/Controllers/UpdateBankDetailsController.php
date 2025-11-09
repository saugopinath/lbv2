<?php

namespace App\Http\Controllers;

use App\Models\Codemaster;
use Illuminate\Http\Request;
use App\Helpers\ChechDupHelper;
use App\Helpers\CheckAuthHelper;
use App\Helpers\WorkFlowPermissionHelper;
use App\Models\AcceptRejectInfo;
use Illuminate\Support\Facades\DB;
use App\Models\BeneficiaryEnclosure;
use Illuminate\Support\Facades\Auth;
use App\Models\BeneficiaryCommonList;
use Illuminate\Support\Facades\Crypt;

class UpdateBankDetailsController extends Controller
{
    protected $doctype;
    protected $isAuthorized = false;
    public function __construct()
    {
        if (CheckAuthHelper::isCommonApprover()) {
            $this->isAuthorized = true;
        } else {
             redirect()->route('dashboard')
                ->with('error', 'Oops! You are not authorized to perform this action.')
                ->send();
        }
    }
    public function index()
    {
           if (WorkFlowPermissionHelper::canUpdateBankDetails()) {
        // if (Auth::user()->can('update bank details')) {
            $header = 'Update Bank Details For Approved Beneficiary';
            return view('UpdateBankDetailsView.bank_deatils_index', compact('header'));
        }
        $header = 'Oops! You do not have permission to update bank details.';
        return view('CommonRestictedpage.index', compact('header'));
    }
    public function updateBeneficiaryBank($type, Request $request)
    {
        if (WorkFlowPermissionHelper::canSearchBankUpdate()) {
        // if (Auth::user()->can('search bank update')) {
            $reportType = 3;
            $doctype = $this->doctype;
            $application_id = Crypt::decryptString($request->application_id);
            $beneficiary_id = Crypt::decryptString($request->beneficiary_id);
            $query = BeneficiaryCommonList::query()
                ->with(['sourceable', 'sourceable.contact', 'sourceable.bank'])
                ->where('sourceable_id', $application_id)
                ->first();

            $mobileNumber = $query?->sourceable?->mobile_no ?? '';

            if ($type === 'bank') {
                return view('UpdateBankDetailsView.bank_deatils_update', compact('application_id', 'reportType'));
            } elseif ($type === 'mobile') {
                return view('UpdateBankDetailsView.mobile_deatils_update', compact('application_id', 'reportType', 'mobileNumber'));
            }
        }
        $header = 'Oops! You do not have permission to search bank update.';
        return view('CommonRestictedpage.index', compact('header'));
    }
    public function updateMobile(Request $request)
    {
        if (WorkFlowPermissionHelper::canUpdateMobile()) {
        // if (Auth::user()->can('update mobile')) {

            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Please login first!');
            }

            $validated = $request->validate([
                'application_id' => 'required|string',
                'mobile' => ['required', 'digits:10'],
                'revert_reason_remarks' => ['required', 'string', 'max:255'],
            ]);

            $application_id = Crypt::decryptString($request->application_id);
            $mobileData     = $request->mobile;
            $reasonRemarks  = $request->revert_reason_remarks;

            $duplicateCheck = $this->checkduplicate($request);
            if ($duplicateCheck !== true) {
                return back()->withErrors(['duplicate_check' => $duplicateCheck])->withInput();
            }

            try {
                DB::beginTransaction();

                $beneficiary = BeneficiaryCommonList::where('sourceable_id', $application_id)
                    ->with('sourceable')
                    ->first();

                if (!$beneficiary) {
                    return back()->with('error', 'Beneficiary not found!');
                }

                if ($beneficiary->sourceable) {
                    $beneficiary->sourceable->updateOrCreate(
                        ['application_id' => $application_id],
                        [
                            'mobile_no'  => $mobileData,
                            'created_by' => Auth::id(),
                        ]
                    );
                }

                $beneficiary->update([
                    'mobile_no' => $mobileData,
                ]);

                $AcceptRejectInfo = new AcceptRejectInfo;
                $AcceptRejectInfo->application_id = $application_id;
                $AcceptRejectInfo->beneficiary_id = $beneficiary->beneficiary_id;
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
                    ->with('success', "Mobile number updated successfully for Application ID: {$application_id}");
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Something went wrong! Mobile number not updated.');
            }
        }
        $header = 'Oops! You do not have permission to update mobile.';
        return view('CommonRestictedpage.index', compact('header'));
    }
    public function updateBank(Request $request)
    {
         if (WorkFlowPermissionHelper::canUpdateBank()) {
        // if (Auth::user()->can('update bank')) {
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Please login first!');
            }

            $application_id = Crypt::decryptString($request->application_id);
            $accountNumber  = $request->bank_account_number;
            $confirmAccData = $request->confirmbankaccountnumber;
            $ifscode        = $request->ifscode;
            $reasonRemarks  = $request->revert_reason_remarks;

            $uploadedDocsCount = BeneficiaryEnclosure::where('application_id', $application_id)
                ->whereIn('document_type', [111])
                ->count();

            $validated = $request->validate([
                'application_id'           => 'required|string',
                'bank_account_number'      => 'required',
                'confirmbankaccountnumber' => 'required|same:bank_account_number',
                'ifscode'                  => 'required|regex:/^[A-Z]{4}0[A-Z0-9]{6}$/i',
                'revert_reason_remarks'    => 'required|string|max:255',
            ], [
                'bank_account_number.required'      => 'Bank Account Number is required.',
                'confirmbankaccountnumber.required' => 'Confirm Bank Account Number is required.',
                'confirmbankaccountnumber.same'     => 'Bank Account Number and Confirm Bank Account Number not match.',
                'ifscode.required'                  => 'IFSC Code is required.',
                'ifscode.regex'                     => 'IFSC Code format is invalid.',
                'revert_reason_remarks.required'    => 'Remarks field is required.',
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

                $beneficiary = BeneficiaryCommonList::where('sourceable_id', $application_id)
                    ->with('sourceable.bank')
                    ->first();

                if (!$beneficiary) {
                    return back()->with('error', 'Beneficiary not found!');
                }

                $beneficiary->sourceable->bank()->updateOrCreate(
                    ['application_id' => $application_id],
                    [
                        'beneficiary_id'      => $beneficiary->beneficiary_id,
                        'bank_account_number' => $confirmAccData,
                        'ifsc'                => $ifscode,
                        'created_by'          => Auth::id(),
                    ]
                );

                $beneficiary->update([
                    'bank_account_number' => $confirmAccData,
                ]);

                $AcceptRejectInfo = new AcceptRejectInfo();
                $AcceptRejectInfo->application_id          = $application_id;
                $AcceptRejectInfo->beneficiary_id          = $beneficiary->beneficiary_id;
                $AcceptRejectInfo->ip_address              = request()->ip();
                $AcceptRejectInfo->user_id                 = Auth::id();
                $AcceptRejectInfo->browser                 = request()->header('User-Agent');
                $AcceptRejectInfo->model_name              = null;
                $AcceptRejectInfo->op_type                 = Codemaster::getIdByCode(0);
                $AcceptRejectInfo->revert_reason_cause_id  = null;
                $AcceptRejectInfo->revert_reason_remarks   = $reasonRemarks;
                $AcceptRejectInfo->parent_id               = AcceptRejectInfo::where('application_id', $application_id)
                    ->latest('id')
                    ->value('id') ?? null;
                $AcceptRejectInfo->save();

                DB::commit();

                return redirect()->route('bankUpdate')
                    ->with('success', "Bank details updated successfully for Application ID: {$application_id}")
                    ->with('warning', $uploadedDocsCount < 1 ? 'Document not found but update completed.' : null);
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Something went wrong! Bank details not updated.');
            }
        }
        $header = 'Oops! You do not have permission to update bank.';
        return view('CommonRestictedpage.index', compact('header'));
    }
    public function checkduplicate(Request $request)
    {
        // dd($request->all());
        $mobileData     = $request->mobile;
        $confirmAccData = $request->confirmbankaccountnumber;

        if (!empty($confirmAccData)) {
            $bankCheck = ChechDupHelper::checkBankMobileDuplicate('bank', $confirmAccData);
            if ($bankCheck !== true) {
                return $bankCheck;
            }
        }

        if (!empty($mobileData)) {
            $mobileCheck = ChechDupHelper::checkBankMobileDuplicate('mobile', $mobileData);
            if ($mobileCheck !== true) {
                return $mobileCheck;
            }
        }

        return true;
    }
}
