<?php

namespace App\Http\Controllers;

use App\Models\Codemaster;
use Illuminate\Http\Request;
use App\Helpers\ChechDupHelper;
use App\Models\AcceptRejectInfo;
use Illuminate\Support\Facades\Crypt;
use App\Models\BeneficiaryTemEnclosure;
use App\Models\ApplicantIncompletDeatil;
use Illuminate\Support\Facades\Validator;
use App\Helpers\AadhaarHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IncompleteTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($stage = 'verifier')
    {
        return view('incomplete_types.index', ['stage' => $stage]);
    }



    public function fullUpdate(Request $request, $id)
    {
        // Decrypt ID
        $realId = Crypt::decrypt($id);

        try {
            // Input Data
            $aadharData     = $request->aadhar_modification;
            $mobileData     = $request->dup_mobile;
            $bankActionData = (int) $request->bank_action;
            $bankAccData    = $request->bank_account_number;
            $confirmAccData = $request->confirmbankaccountnumber;
            $ifscodeData    = $request->ifscode;

            // Get all issues
            $allIssues = ApplicantIncompletDeatil::where('application_id', $realId)->get();
            $applicantInfo = $allIssues->first()?->beneficiaryCommonList;

            if ($allIssues->isEmpty()) {
                return redirect()->back()->with('error', 'Application not found!');
            }

            // Perform duplicate check
            $duplicateCheck = $this->checkduplicate($request, $id);
            if ($duplicateCheck !== true) {
                return redirect()->back()->withErrors(['duplicate_check' => $duplicateCheck])->withInput();
            }

            // Rules and messages
            $rules = [];
            $messages = [];

            foreach ($allIssues as $item) {
                $typeCode = $item->incomplet_type ?? null;

                // Aadhaar check
                if (in_array($typeCode, ['141', '149', '1414'])) {
                    $rules['aadhaar'] = 'required|digits:12';
                    $messages['aadhaar.required'] = 'Aadhaar number is required.';
                    $messages['aadhaar.digits']   = 'Aadhaar number must be exactly 12 digits.';

                    $uploadedDocsCount = BeneficiaryTemEnclosure::where('application_id', $realId)
                        ->whereIn('document_type', [108])
                        ->count();

                    if ($uploadedDocsCount < 1) {
                        $rules['document_upload'] = 'required';
                        $messages['document_upload.required'] = 'Please upload the required document.';
                    }
                }

                // Mobile check
                if (in_array($typeCode, ['142', '1410'])) {
                    $rules['mobile'] = 'required|digits:10';
                    $messages['mobile.required'] = 'Mobile number is required.';
                    $messages['mobile.digits']   = 'Mobile number must be exactly 10 digits.';
                }

                // Bank check
                if (in_array($typeCode, ['145', '146', '1411', '1412', '1413'])) {
                    $rules['bank_action'] = 'required|in:1,2,3,4';
                    $messages['bank_action.required'] = 'Invalid bank action selected.';
                    $messages['bank_action.in'] = 'Operation Type is required.';

                    if (in_array($bankActionData, [2, 3])) {
                        $rules['bank_account_number'] = 'required|digits_between:9,18';
                        $rules['confirmbankaccountnumber'] = 'required|same:bank_account_number';
                        $rules['ifscode'] = 'required|regex:/^[A-Z]{4}0[A-Z0-9]{6}$/i';

                        $messages['bank_account_number.required'] = 'Bank Account Number is required.';
                        $messages['bank_account_number.digits_between'] = 'Bank Account Number must be between 9 to 18 digits.';
                        $messages['confirmbankaccountnumber.required'] = 'Confirm Bank Account Number is required.';
                        $messages['confirmbankaccountnumber.same'] = 'Bank Account Number and Confirm Bank Account Number not match.';
                        $messages['ifscode.required'] = 'IFSC Code is required.';
                        $messages['ifscode.regex'] = 'IFSC Code format is invalid.';

                        $uploadedDocsCount = BeneficiaryTemEnclosure::where('application_id', $realId)
                            ->whereIn('document_type', [112])
                            ->count();

                        if ($uploadedDocsCount < 1) {
                            $rules['document_upload'] = 'required';
                            $messages['document_upload.required'] = 'Please upload the required document.';
                        }
                    }
                }
            }

            // Validator run
            $validator = Validator::make(
                [
                    'aadhaar' => $aadharData,
                    'mobile'  => $mobileData,
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
                return redirect()->back()->withErrors($validator)->withInput();
            }

            // Extra Aadhaar validation (without closure)
            if (in_array($typeCode, ['141', '149', '1414']) && !AadhaarHelper::validate($aadharData)) {
                return redirect()->back()
                    ->withErrors(['aadhaar' => 'Invalid Aadhaar number.'])
                    ->withInput();
            }

            DB::beginTransaction();

            $select_lgd = session('lgd_session');
            $user_id    = Crypt::decryptString($select_lgd['role_id']);

            // Create request log
            $acceptReject = AcceptRejectInfo::create([
                'application_id'         => $realId,
                'beneficiary_id'         => $applicantInfo->beneficiary_id,
                'ip_address'             => $request->ip(),
                'user_id'                => $user_id,
                'browser'                => $request->header('User-Agent'),
                'model_name'             => 'ApplicantIncompleteDetail',
                'op_type'                => Codemaster::where('code', 245)->value('id'),
                'revert_reason_cause_id' => null,
                'revert_reason_remarks'  => null,
                'parent_id'              => null,
            ]);

            // Bank related extra checks
            $bankIssues = $allIssues->filter(
                fn($i) => in_array($i->incomplet_type, ['145', '146', '1411', '1412', '1413'])
            );
            $dupbankacc = $bankIssues->contains(fn($i) => $i->incomplet_type == '1411');

            // Update loop
            foreach ($allIssues as $item) {
                $typeCode = $item->incomplet_type ?? null;
                if (!$typeCode) continue;

                $jsonValue = [];

                // Aadhaar related
                if (in_array($typeCode, ['141', '149', '1414'])) {
                    $jsonValue = [
                        'aadhaar_no'     => $aadharData,
                    ];
                }

                // Mobile related
                elseif (in_array($typeCode, ['142', '1410'])) {
                    $jsonValue = [
                        'mobile_no'      => $mobileData,
                    ];
                }

                // Bank related
                elseif (in_array($typeCode, ['145', '146', '1411', '1412', '1413'])) {
                    $jsonValue = [
                        'ifscode'                 => $ifscodeData,
                        'bank_account_number'     => $bankAccData,
                        'confirmbankaccountnumber' => $confirmAccData,
                    ];

                    $relatedIssues = $allIssues->whereIn('incomplet_type', ['145', '146', '1411', '1412', '1413']);

                    if ($relatedIssues->count() === 1) {
                        $isActive = 1;
                        $updateValue = $jsonValue;
                    } else {
                        if ($typeCode == '1411') {
                            $isActive = 1;
                            $updateValue = $jsonValue;
                        } else {
                            if ($dupbankacc) {
                                $isActive = 0;
                                $updateValue = null;
                            } else {
                                $isActive = ($bankActionData == 1 ? 1 : 0);
                                $updateValue = $jsonValue;
                            }
                        }
                    }

                    $item->update([
                        'new_value'             => $updateValue,
                        'change_type'           => $bankActionData ?? null,
                        'next_level_request_id' => 1,
                        'request_id'            => $acceptReject->id,
                        'is_active'             => $isActive,
                    ]);

                    continue;
                }

                if (!empty($jsonValue)) {
                    $item->update([
                        'new_value'             => $jsonValue,
                        'change_type'           => null,
                        'next_level_request_id' => 1,
                        'request_id'            => $acceptReject->id,
                    ]);
                }
            }

            DB::commit();

            session()->flash('success', "Request sent to Approver for approval for the Application ID: {$realId}");
            return redirect()->route('incomplete.types', ['stage' => 'verifier', 'id' => $realId]);
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error("Full Update Failed: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage())->withInput();
        }
    }

    public function checkduplicate(Request $request, $id)
    {
        $realId = Crypt::decrypt($id);

        $aadharData     = $request->aadhar_modification;
        $mobileData     = $request->dup_mobile;
        $confirmAccData = $request->confirmbankaccountnumber;

        $allIssues = ApplicantIncompletDeatil::where('application_id', $realId)->get();

        if ($allIssues->isEmpty()) {
            return true; // No issues found, no need for duplicate check
        }

        foreach ($allIssues as $item) {
            $typeCode = $item->incomplet_type ?? null;

            if (in_array($typeCode, ['141', '149', '1414']) && $aadharData) {
                $result = ChechDupHelper::checkDuplicate('aadhaar', $aadharData, $item->beneficiaryCommonList);
                if ($result !== true) {
                    return $result; // Return error message
                }
            } elseif (in_array($typeCode, ['142', '1410']) && $mobileData) {
                $result = ChechDupHelper::checkDuplicate('mobile', $mobileData, $item->beneficiaryCommonList);
                if ($result !== true) {
                    return $result; // Return error message
                }
            } elseif (in_array($typeCode, ['145', '146', '1411', '1412', '1413']) && $confirmAccData) {
                $result = ChechDupHelper::checkDuplicate('bank', $confirmAccData, $item->beneficiaryCommonList);
                if ($result !== true) {
                    return $result; // Return error message
                }
            }
        }

        return true; // No duplicates found
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
