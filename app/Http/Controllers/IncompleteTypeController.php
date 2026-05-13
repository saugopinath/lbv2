<?php

namespace App\Http\Controllers;

use App\Helpers\AadhaarHelper;
use App\Helpers\ChechDupHelper;
use App\Helpers\CheckAuthHelper;
use App\Helpers\WorkFlowPermissionHelper;
use App\Models\AcceptRejectInfo;
use App\Models\ApplicantIncompleteDetail;
use App\Models\BeneficiaryTemEnclosure;
use App\Models\Codemaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class IncompleteTypeController extends Controller
{
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
    }

    public function index($stage)
    {
        if ($stage === 'verifier' && WorkFlowPermissionHelper::canVerifierIncomplete()) {
            return view('incomplete_types.index', ['stage' => 'verifier']);
        }

        if ($stage === 'approver' && WorkFlowPermissionHelper::canApproverIncomplete()) {
            return view('incomplete_types.index', ['stage' => 'approver']);
        }

        $header = 'Oops! You do not have permission to view this incomplete stage.';

        return view('CommonRestictedpage.index', compact('header'));
    }

    public function fullUpdate(Request $request, $id, $schemeId)
    {
        if (WorkFlowPermissionHelper::canUpdateIncomplete()) {
            $realId = Crypt::decrypt($id);
            $schemeId = Crypt::decrypt($schemeId);

            try {
                $aadharData = $request->aadhaar_modification;
                $rawMobileData = $request->dup_mobile;
                $mobileData = is_array($rawMobileData) ? ($rawMobileData[$realId] ?? null) : $rawMobileData;
                $bankActionData = (int) $request->bank_action;
                $bankAccData = $request->bank_account_number;
                $confirmAccData = $request->confirmbankaccountnumber;
                $ifscodeData = $request->ifscode;

                $allIssues = ApplicantIncompleteDetail::where('application_id', $realId)->where('scheme_id', $schemeId)->get();
                $applicantInfo = $allIssues->first()?->personaldetails;

                if ($allIssues->isEmpty()) {
                    return back()->with('error', 'Application not found!');
                }

                $duplicateCheck = $this->checkduplicate($request, $id, $schemeId);
                if ($duplicateCheck !== true) {
                    return back()->withErrors(['duplicate_check' => $duplicateCheck])->withInput();
                }

                $rules = [];
                $messages = [];

                foreach ($allIssues as $item) {
                    $typeCode = $item->incomplete_type ?? null;

                    if (! $typeCode) {
                        continue;
                    }

                    // Aadhaar checks
                    if (in_array($typeCode, ['141', '149', '1414'])) {
                        $rules['aadhaar'] = 'required|digits:12';
                        $messages += [
                            'aadhaar.required' => 'Aadhaar number is required.',
                            'aadhaar.digits' => 'Aadhaar number must be exactly 12 digits.',
                        ];

                        $uploadedDocsCount = BeneficiaryTemEnclosure::where('application_id', $realId)
                            ->whereIn('document_type', [107])
                            ->count();

                        if ($uploadedDocsCount < 1) {
                            $rules['document_upload'] = 'required';
                            $messages['document_upload.required'] = 'Please upload the required document.';
                        }
                    }

                    // Mobile checks
                    if (in_array($typeCode, ['142', '1410'])) {
                        $rules['mobile'] = 'required|digits:10';
                        $messages += [
                            'mobile.required' => 'Mobile number is required.',
                            'mobile.digits' => 'Mobile number must be exactly 10 digits.',
                        ];
                    }

                    // Bank checks
                    if (in_array($typeCode, ['145', '146', '1411', '1412', '1413'])) {
                        $rules['bank_action'] = 'required|in:1,2,3,4';
                        $messages += [
                            'bank_action.required' => 'Invalid bank action selected.',
                            'bank_action.in' => 'Operation Type is required.',
                        ];

                        if (in_array($bankActionData, [2, 3])) {
                            $rules += [
                                'bank_account_number' => 'required',
                                'confirmbankaccountnumber' => 'required|same:bank_account_number',
                                'ifscode' => 'required|regex:/^[A-Z]{4}0[A-Z0-9]{6}$/i',
                            ];

                            $messages += [
                                'bank_account_number.required' => 'Bank Account Number is required.',
                                'bank_account_number.digits_between' => 'Bank Account Number must be between 9 to 18 digits.',
                                'confirmbankaccountnumber.required' => 'Confirm Bank Account Number is required.',
                                'confirmbankaccountnumber.same' => 'Bank Account Number and Confirm Bank Account Number not match.',
                                'ifscode.required' => 'IFSC Code is required.',
                                'ifscode.regex' => 'IFSC Code format is invalid.',
                            ];

                            $uploadedDocsCount = BeneficiaryTemEnclosure::where('application_id', $realId)
                                ->whereIn('document_type', [111])
                                ->count();

                            if ($uploadedDocsCount < 1) {
                                $rules['document_upload'] = 'required';
                                $messages['document_upload.required'] = 'Please upload the required document.';
                            }
                        }
                    }
                }

                $validator = Validator::make(
                    [
                        'aadhaar' => $aadharData,
                        'mobile' => $mobileData,
                        'bank_action' => $bankActionData,
                        'bank_account_number' => $bankAccData,
                        'confirmbankaccountnumber' => $confirmAccData,
                        'ifscode' => $ifscodeData,
                        'document_upload' => null,
                    ],
                    $rules,
                    $messages
                );

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                foreach ($allIssues as $item) {

                    $typeCode = $item->incomplete_type ?? null;

                    if (in_array($typeCode, ['141', '149', '1414'])) {

                        if (! AadhaarHelper::validate($aadharData)) {
                            return back()
                                ->withErrors(['aadhaar' => 'Invalid Aadhaar number.'])
                                ->withInput();
                        }

                    }
                }

                DB::beginTransaction();

                try {
                    $select_lgd = session('lgd_session');
                    $user_id = Crypt::decryptString($select_lgd['role_id']);
                    // Log request
                    $acceptReject = AcceptRejectInfo::create([
                        'application_id' => $realId,
                        'scheme_id' => $schemeId,
                        'beneficiary_id' => $applicantInfo->beneficiary_id,
                        'ip_address' => $request->ip(),
                        'user_id' => $user_id,
                        'browser' => $request->header('User-Agent'),
                        'model_name' => class_basename(Route::current()->controller).'@'.Route::getCurrentRoute()->getActionMethod(),
                        'op_type' => Codemaster::where('code', 2103)->value('id'),
                    ]);

                    $bankIssues = $allIssues->filter(fn ($i) => in_array($i->incomplete_type, ['145', '146', '1411', '1412', '1413']));
                    $dupbankacc = $bankIssues->contains(fn ($i) => $i->incomplete_type == '1411');

                    // ✅ Step 6: Update loop
                    foreach ($allIssues as $item) {
                        $typeCode = $item->incomplete_type ?? null;
                        if (! $typeCode) {
                            continue;
                        }

                        $jsonValue = [];
                        $isActive = 1;
                        $updateValue = null;

                        // Aadhaar
                        if (in_array($typeCode, ['141', '149', '1414'])) {
                            $jsonValue = ['aadhaar_no' => $aadharData];
                        }

                        // Mobile
                        elseif (in_array($typeCode, ['142', '1410'])) {
                            $jsonValue = ['mobile_no' => $mobileData];
                        }

                        // Bank
                        elseif (in_array($typeCode, ['145', '146', '1411', '1412', '1413'])) {
                            $jsonValue = [
                                'ifscode' => $ifscodeData,
                                'bank_account_number' => $bankAccData,
                                'confirmbankaccountnumber' => $confirmAccData,
                            ];

                            $relatedIssues = $allIssues->whereIn('incomplete_type', ['145', '146', '1411', '1412', '1413']);

                            if ($relatedIssues->count() > 1) {
                                if ($typeCode == '1411') {
                                    $isActive = 1;
                                } else {
                                    $isActive = $dupbankacc ? 0 : ($bankActionData == 1 ? 1 : 0);
                                }
                            }

                            $item->update([
                                'new_value' => $jsonValue,
                                'change_type' => $bankActionData ?? null,
                                'next_level_request_id' => 1,
                                'request_id' => $acceptReject->id,
                                'is_active' => $isActive,
                            ]);

                            continue;
                        }

                        if (! empty($jsonValue)) {
                            $item->update([
                                'new_value' => $jsonValue,
                                'change_type' => null,
                                'next_level_request_id' => 1,
                                'request_id' => $acceptReject->id,
                            ]);
                        }
                    }

                    DB::commit();

                    session()->flash('success', "Request sent to Approver for Application ID: {$realId}");

                    return redirect()->route('incomplete.types', ['stage' => 'verifier', 'id' => $realId]);
                } catch (\Exception $innerEx) {
                    DB::rollBack();

                    return back()->with('error', 'DB transaction failed: '.$innerEx->getMessage())->withInput();
                }
            } catch (\Exception $e) {
                return back()->with('error', 'Unexpected error: '.$e->getMessage())->withInput();
            }
        }
        $header = 'Oops! You do not have permission to update incomplete.';

        return view('CommonRestictedpage.index', compact('header'));
    }

    public function revertVerify(Request $request, $id, $schemeId)
    {
        if (WorkFlowPermissionHelper::canRevertIncomplete()) {
            $realId = Crypt::decrypt($id);
            $schemeId = Crypt::decrypt($schemeId);

            try {
                $aadharData = $request->aadhaar_modification;
                $rawMobileData = $request->dup_mobile;
                $mobileData = is_array($rawMobileData) ? ($rawMobileData[$realId] ?? null) : $rawMobileData;
                $bankActionData = (int) $request->bank_action;
                $bankAccData = $request->bank_account_number;
                $confirmAccData = $request->confirmbankaccountnumber;
                $ifscodeData = $request->ifscode;

                // Get all issues
                $allIssues = ApplicantIncompleteDetail::where('application_id', $realId)->where('scheme_id', $schemeId)->get();
                $applicantInfo = $allIssues->first()?->personaldetails;

                if ($allIssues->isEmpty()) {
                    return back()->with('error', 'Application not found!');
                }

                $duplicateCheck = $this->checkduplicate($request, $id, $schemeId);
                if ($duplicateCheck !== true) {
                    return back()->withErrors(['duplicate_check' => $duplicateCheck])->withInput();
                }

                $rules = [];
                $messages = [];

                foreach ($allIssues as $item) {
                    $typeCode = $item->incomplete_type ?? null;
                    if (! $typeCode) {
                        continue;
                    }

                    // Aadhaar
                    if (in_array($typeCode, ['141', '149', '1414'])) {
                        $rules['aadhaar'] = 'required|digits:12';
                        $messages += [
                            'aadhaar.required' => 'Aadhaar number is required.',
                            'aadhaar.digits' => 'Aadhaar number must be exactly 12 digits.',
                        ];

                        $uploadedDocsCount = BeneficiaryTemEnclosure::where('application_id', $realId)
                            ->whereIn('document_type', [107])
                            ->count();

                        if ($uploadedDocsCount < 1) {
                            $rules['document_upload'] = 'required';
                            $messages['document_upload.required'] = 'Please upload the required document.';
                        }
                    }

                    // Mobile
                    if (in_array($typeCode, ['142', '1410'])) {
                        $rules['mobile'] = 'required|digits:10';
                        $messages += [
                            'mobile.required' => 'Mobile number is required.',
                            'mobile.digits' => 'Mobile number must be exactly 10 digits.',
                        ];
                    }

                    // Bank
                    if (in_array($typeCode, ['145', '146', '1411', '1412', '1413'])) {
                        $rules['bank_action'] = 'required|in:1,2,3,4';
                        $messages += [
                            'bank_action.required' => 'Invalid bank action selected.',
                            'bank_action.in' => 'Operation Type is required.',
                        ];

                        if (in_array($bankActionData, [2, 3])) {
                            $rules += [
                                'bank_account_number' => 'required',
                                'confirmbankaccountnumber' => 'required|same:bank_account_number',
                                'ifscode' => 'required|regex:/^[A-Z]{4}0[A-Z0-9]{6}$/i',
                            ];

                            $messages += [
                                'bank_account_number.required' => 'Bank Account Number is required.',
                                'bank_account_number.digits_between' => 'Bank Account Number must be between 9 to 18 digits.',
                                'confirmbankaccountnumber.required' => 'Confirm Bank Account Number is required.',
                                'confirmbankaccountnumber.same' => 'Bank Account Number and Confirm Bank Account Number not match.',
                                'ifscode.required' => 'IFSC Code is required.',
                                'ifscode.regex' => 'IFSC Code format is invalid.',
                            ];

                            $uploadedDocsCount = BeneficiaryTemEnclosure::where('application_id', $realId)
                                ->whereIn('document_type', [111])
                                ->count();

                            if ($uploadedDocsCount < 1) {
                                $rules['document_upload'] = 'required';
                                $messages['document_upload.required'] = 'Please upload the required document.';
                            }
                        }
                    }
                }

                $validator = Validator::make([
                    'aadhaar' => $aadharData,
                    'mobile' => $mobileData,
                    'bank_action' => $bankActionData,
                    'bank_account_number' => $bankAccData,
                    'confirmbankaccountnumber' => $confirmAccData,
                    'ifscode' => $ifscodeData,
                    'document_upload' => null,
                ], $rules, $messages);

                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }

                // Aadhaar validity
                if (isset($aadharData) && ! empty($aadharData)) {
                    if (! AadhaarHelper::validate($aadharData)) {
                        return back()->withErrors(['aadhaar' => 'Invalid Aadhaar number.'])->withInput();
                    }
                }

                DB::beginTransaction();

                $select_lgd = session('lgd_session');
                $user_id = Crypt::decryptString($select_lgd['role_id']);

                $previousId = AcceptRejectInfo::where('application_id', $realId)
                    ->where('scheme_id', $schemeId)
                    ->orderByDesc('id')
                    ->value('id');
               
                $acceptRejectInfo = AcceptRejectInfo::create([
                    'application_id' => $realId,
                    'beneficiary_id' => $applicantInfo->beneficiary_id ?? null,
                    'scheme_id' => $schemeId,
                    'ip_address' => $request->ip(),
                    'user_id' => $user_id,
                    'browser' => $request->header('User-Agent'),
                    'model_name' => class_basename(Route::current()->controller).'@'.Route::getCurrentRoute()->getActionMethod(),
                    'op_type' => Codemaster::where('code', 2103)->value('id'),
                    'revert_reason_cause_id' => null,
                    'revert_reason_remarks' => null,
                    'parent_id' => $previousId,
                ]);

                $bankIssues = $allIssues->filter(fn ($i) => in_array($i->incomplete_type, ['145', '146', '1411', '1412', '1413']));
                $dupbankacc = $bankIssues->contains(fn ($i) => $i->incomplete_type == '1411');

                foreach ($allIssues as $item) {
                    $typeCode = $item->incomplete_type ?? null;
                    if (! $typeCode) {
                        continue;
                    }

                    $jsonValue = [];
                    $isActive = 1;
                    $updateValue = null;

                    // Aadhaar
                    if (in_array($typeCode, ['141', '149', '1414'])) {
                        $jsonValue = ['aadhaar_no' => $aadharData];
                    }
                    // Mobile
                    elseif (in_array($typeCode, ['142', '1410'])) {
                        $jsonValue = ['mobile_no' => $mobileData];
                    }
                    // Bank
                    elseif (in_array($typeCode, ['145', '146', '1411', '1412', '1413'])) {
                        $jsonValue = [
                            'ifscode' => $ifscodeData,
                            'bank_account_number' => $bankAccData,
                            'confirmbankaccountnumber' => $confirmAccData,
                        ];

                        $relatedIssues = $allIssues->whereIn('incomplete_type', ['145', '146', '1411', '1412', '1413']);

                        if ($relatedIssues->count() === 1) {
                            $isActive = 1;
                        } else {
                            if ($typeCode == '1411') {
                                $isActive = 1;
                            } else {
                                $isActive = $dupbankacc ? 0 : ($bankActionData == 1 ? 1 : 0);
                            }
                        }

                        $item->update([
                            'new_value' => $jsonValue,
                            'change_type' => $bankActionData ?? null,
                            'next_level_request_id' => 1,
                            'request_id' => $acceptRejectInfo->id,
                            'is_active' => $isActive,
                        ]);

                        continue;
                    }

                    if (! empty($jsonValue)) {
                        $item->update([
                            'new_value' => $jsonValue,
                            'change_type' => $bankActionData ?? null,
                            'next_level_request_id' => 1,
                            'request_id' => $acceptRejectInfo->id,
                        ]);
                    }
                }

                DB::commit();

                session()->flash('success', "Revert details updated and sent to approver for Application ID: {$realId}");

                return redirect()->route('incomplete.types', ['stage' => 'revert', 'id' => $realId]);
            } catch (\Throwable $e) {
                DB::rollBack();
                session()->flash('error', 'Something went wrong while reverting. Please try again.');

                return back()->withInput();
            }
        }
        $header = 'Oops! You do not have permission to revert incomplete.';

        return view('CommonRestictedpage.index', compact('header'));
    }

    public function checkduplicate(Request $request, $id, $schemeId)
    {
        $realId = Crypt::decrypt($id);

        $aadharData = $request->aadhaar_modification;
        $rawMobileData = $request->dup_mobile;
        $mobileData = is_array($rawMobileData) ? ($rawMobileData[$realId] ?? null) : $rawMobileData;
        $confirmAccData = $request->confirmbankaccountnumber;
        $allIssues = ApplicantIncompleteDetail::where('application_id', $realId)->where('scheme_id', $schemeId)->get();

        if ($allIssues->isEmpty()) {
            return true; // No issues found, no need for duplicate check
        }

        foreach ($allIssues as $item) {
            $typeCode = $item->incomplete_type ?? null;

            if (in_array($typeCode, ['141', '149', '1414']) && $aadharData) {
                $result = ChechDupHelper::checkDuplicate('aadhaar', $aadharData, $item->incomplete_type, $schemeId);
                if ($result !== true) {
                    return $result; // Return error message
                }
            } elseif (in_array($typeCode, ['142', '1410']) && $mobileData) {
                $result = ChechDupHelper::checkDuplicate('mobile', $mobileData, $item->incomplete_type, $schemeId);
                if ($result !== true) {
                    return $result; // Return error message
                }
            } elseif (in_array($typeCode, ['145', '146', '1411', '1412', '1413']) && $confirmAccData) {
                $result = ChechDupHelper::checkDuplicate('bank', $confirmAccData, $item->incomplete_type, $schemeId);
                if ($result !== true) {
                    return $result; // Return error message
                }
            }
        }

        return true; // No duplicates found
    }

    public function incompleteDetails()
    {
        return view('incomplete.incompleteDetails');
    }
}
