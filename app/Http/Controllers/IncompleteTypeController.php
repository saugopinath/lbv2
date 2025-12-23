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
use App\Models\DraftBeneficiaryPersonal;
use App\Models\BeneficiaryCommonList;
use App\Helpers\AadhaarHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Exports\ArrayExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use App\Helpers\CheckAuthHelper;
use App\Helpers\WorkFlowPermissionHelper;
use App\Helpers\LgdFilterHelper;
use Illuminate\Support\Facades\Route;

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
        // dd($stage);
        if ($stage === 'verifier' && WorkFlowPermissionHelper::canVerifierIncomplet()) {
            // dd('ok1');
            // if ($stage === 'verifier' && $user->can('view verifier incomplete')) {
            return view('incomplete_types.index', ['stage' => 'verifier']);
        }

        if ($stage === 'approver' && WorkFlowPermissionHelper::canApproverIncomplet()) {
            // dd('ok');
            // if ($stage === 'approver' && $user->can('view approver incomplete')) {
            return view('incomplete_types.index', ['stage' => 'approver']);
        }

        $header = 'Oops! You do not have permission to view this incomplete stage.';
        return view('CommonRestictedpage.index', compact('header'));
    }
    public function fullUpdate(Request $request, $id)
    {
        // if (WorkFlowPermissionHelper::canUpdateIncomplet()) {
        // if (Auth::user()->can('update incomplete')) {
        $realId = Crypt::decrypt($id);

        // Wrap everything in try-catch
        try {
            // Collect input
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
                return back()->with('error', 'Application not found!');
            }

            // ✅ Step 1: Duplicate check before proceeding
            $duplicateCheck = $this->checkduplicate($request, $id);
            if ($duplicateCheck !== true) {
                return back()->withErrors(['duplicate_check' => $duplicateCheck])->withInput();
            }
            // dd('ok');
            // ✅ Step 2: Validation rules setup
            $rules = [];
            $messages = [];

            foreach ($allIssues as $item) {
                $typeCode = $item->incomplet_type ?? null;
                if (!$typeCode) continue;

                // Aadhaar checks
                if (in_array($typeCode, ['141', '149', '1414'])) {
                    $rules['aadhaar'] = 'required|digits:12';
                    $messages += [
                        'aadhaar.required' => 'Aadhaar number is required.',
                        'aadhaar.digits'   => 'Aadhaar number must be exactly 12 digits.',
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
                        'mobile.digits'   => 'Mobile number must be exactly 10 digits.',
                    ];
                }

                // Bank checks
                if (in_array($typeCode, ['145', '146', '1411', '1412', '1413'])) {
                    $rules['bank_action'] = 'required|in:1,2,3,4';
                    $messages += [
                        'bank_action.required' => 'Invalid bank action selected.',
                        'bank_action.in'       => 'Operation Type is required.',
                    ];

                    if (in_array($bankActionData, [2, 3])) {
                        $rules += [
                            'bank_account_number'      => 'required',
                            'confirmbankaccountnumber' => 'required|same:bank_account_number',
                            'ifscode'                  => 'required|regex:/^[A-Z]{4}0[A-Z0-9]{6}$/i',
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

            // ✅ Step 3: Validation check
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
                return back()->withErrors($validator)->withInput();
            }

            // ✅ Step 4: Aadhaar logical validation
            if (in_array($typeCode ?? '', ['141', '149', '1414']) && !AadhaarHelper::validate($aadharData)) {
                return back()
                    ->withErrors(['aadhaar' => 'Invalid Aadhaar number.'])
                    ->withInput();
            }

            // ✅ Step 5: Begin DB transaction safely
            DB::beginTransaction();

            try {
                $select_lgd = session('lgd_session');
                $user_id = Crypt::decryptString($select_lgd['role_id']);

                // Log request
                $acceptReject = AcceptRejectInfo::create([
                    'application_id'         => $realId,
                    'beneficiary_id'         => $applicantInfo->beneficiary_id,
                    'ip_address'             => $request->ip(),
                    'user_id'                => $user_id,
                    'browser'                => $request->header('User-Agent'),
                    'model_name'             => class_basename(Route::current()->controller) . '@' . Route::getCurrentRoute()->getActionMethod(),
                    'op_type'                => Codemaster::where('code', 2103)->value('id'),
                ]);

                $bankIssues = $allIssues->filter(fn($i) => in_array($i->incomplet_type, ['145', '146', '1411', '1412', '1413']));
                $dupbankacc = $bankIssues->contains(fn($i) => $i->incomplet_type == '1411');

                // ✅ Step 6: Update loop
                foreach ($allIssues as $item) {
                    $typeCode = $item->incomplet_type ?? null;
                    if (!$typeCode) continue;

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
                            'ifscode'                 => $ifscodeData,
                            'bank_account_number'     => $bankAccData,
                            'confirmbankaccountnumber' => $confirmAccData,
                        ];

                        $relatedIssues = $allIssues->whereIn('incomplet_type', ['145', '146', '1411', '1412', '1413']);

                        if ($relatedIssues->count() > 1) {
                            if ($typeCode == '1411') {
                                $isActive = 1;
                            } else {
                                $isActive = $dupbankacc ? 0 : ($bankActionData == 1 ? 1 : 0);
                            }
                        }

                        $item->update([
                            'new_value'             => $jsonValue,
                            'change_type'           => $bankActionData ?? null,
                            'next_level_request_id' => 1,
                            'request_id'            => $acceptReject->id,
                            'is_active'             => $isActive,
                        ]);

                        continue;
                    }

                    // Generic update
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

                session()->flash('success', "Request sent to Approver for Application ID: {$realId}");
                return redirect()->route('incomplete.types', ['stage' => 'verifier', 'id' => $realId]);
            } catch (\Exception $innerEx) {
                DB::rollBack();
                return back()->with('error', 'DB transaction failed: ' . $innerEx->getMessage())->withInput();
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Unexpected error: ' . $e->getMessage())->withInput();
        }
        // }
        // $header = 'Oops! You do not have permission to update incomplete.';
        // return view('CommonRestictedpage.index', compact('header'));
    }
    public function revertVerify(Request $request, $id)
    {
        // if (WorkFlowPermissionHelper::canRevertIncomplet()) {
        // if (Auth::user()->can('revert incomplete')) {
        $realId = Crypt::decrypt($id);

        try {
            // ---------------------------------------------------------
            //  Step 1: Get related data
            // ---------------------------------------------------------
            $aadharData     = $request->aadhar_modification;
            $mobileData     = $request->dup_mobile;
            $bankActionData = (int) $request->bank_action;
            $bankAccData    = $request->bank_account_number;
            $confirmAccData = $request->confirmbankaccountnumber;
            $ifscodeData    = $request->ifscode;

            $allIssues = ApplicantIncompletDeatil::where('application_id', $realId)->get();
            $applicantInfo = $allIssues->first()?->beneficiaryCommonList;

            if ($allIssues->isEmpty()) {
                return back()->with('error', 'Application not found!');
            }

            // ---------------------------------------------------------
            //  Step 2: Duplicate check
            // ---------------------------------------------------------
            $duplicateCheck = $this->checkduplicate($request, $id);
            if ($duplicateCheck !== true) {
                return back()->withErrors(['duplicate_check' => $duplicateCheck])->withInput();
            }

            // ---------------------------------------------------------
            //  Step 3: Validation setup
            // ---------------------------------------------------------
            $rules = [];
            $messages = [];

            foreach ($allIssues as $item) {
                $typeCode = $item->incomplet_type ?? null;
                if (!$typeCode) continue;

                // Aadhaar
                if (in_array($typeCode, ['141', '149', '1414'])) {
                    $rules['aadhaar'] = 'required|digits:12';
                    $messages += [
                        'aadhaar.required' => 'Aadhaar number is required.',
                        'aadhaar.digits'   => 'Aadhaar number must be exactly 12 digits.',
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
                        'mobile.digits'   => 'Mobile number must be exactly 10 digits.',
                    ];
                }

                // Bank
                if (in_array($typeCode, ['145', '146', '1411', '1412', '1413'])) {
                    $rules['bank_action'] = 'required|in:1,2,3,4';
                    $messages += [
                        'bank_action.required' => 'Invalid bank action selected.',
                        'bank_action.in'       => 'Operation Type is required.',
                    ];

                    if (in_array($bankActionData, [2, 3])) {
                        $rules += [
                            'bank_account_number'      => 'required',
                            'confirmbankaccountnumber' => 'required|same:bank_account_number',
                            'ifscode'                  => 'required|regex:/^[A-Z]{4}0[A-Z0-9]{6}$/i',
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

            // ---------------------------------------------------------
            //  Step 4: Run validation
            // ---------------------------------------------------------
            $validator = Validator::make([
                'aadhaar' => $aadharData,
                'mobile'  => $mobileData,
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
            if (isset($aadharData) && !empty($aadharData)) {
                if (!AadhaarHelper::validate($aadharData)) {
                    return back()->withErrors(['aadhaar' => 'Invalid Aadhaar number.'])->withInput();
                }
            }

            // ---------------------------------------------------------
            //  Step 5: Database transaction
            // ---------------------------------------------------------
            DB::beginTransaction();

            $select_lgd = session('lgd_session');
            $user_id = Crypt::decryptString($select_lgd['role_id']);

            $previousId = AcceptRejectInfo::where('application_id', $realId)
                ->orderByDesc('id')
                ->value('id');

            // ✅ Correct variable name
            $acceptRejectInfo = AcceptRejectInfo::create([
                'application_id'         => $realId,
                'beneficiary_id'         => $applicantInfo->beneficiary_id ?? null,
                'ip_address'             => $request->ip(),
                'user_id'                => $user_id,
                'browser'                => $request->header('User-Agent'),
                'model_name'             => class_basename(Route::current()->controller) . '@' . Route::getCurrentRoute()->getActionMethod(),
                'op_type'                => Codemaster::where('code', 2103)->value('id'),
                'revert_reason_cause_id' => null,
                'revert_reason_remarks'  => null,
                'parent_id'              => $previousId,
            ]);

            $bankIssues = $allIssues->filter(fn($i) => in_array($i->incomplet_type, ['145', '146', '1411', '1412', '1413']));
            $dupbankacc = $bankIssues->contains(fn($i) => $i->incomplet_type == '1411');

            foreach ($allIssues as $item) {
                $typeCode = $item->incomplet_type ?? null;
                if (!$typeCode) continue;

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
                        'ifscode'                 => $ifscodeData,
                        'bank_account_number'     => $bankAccData,
                        'confirmbankaccountnumber' => $confirmAccData,
                    ];

                    $relatedIssues = $allIssues->whereIn('incomplet_type', ['145', '146', '1411', '1412', '1413']);

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
                        'new_value'             => $jsonValue,
                        'change_type'           => $bankActionData ?? null,
                        'next_level_request_id' => 1,
                        'request_id'            => $acceptRejectInfo->id,
                        'is_active'             => $isActive,
                    ]);

                    continue;
                }

                if (!empty($jsonValue)) {
                    $item->update([
                        'new_value'             => $jsonValue,
                        'change_type'           => $bankActionData ?? null,
                        'next_level_request_id' => 1,
                        'request_id'            => $acceptRejectInfo->id,
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
        // }
        // $header = 'Oops! You do not have permission to revert incomplete.';
        // return view('CommonRestictedpage.index', compact('header'));
    }
    public function checkduplicate(Request $request, $id)
    {
        $realId = Crypt::decrypt($id);

        $aadharData     = $request->aadhar_modification;
        $mobileData     = $request->dup_mobile;
        $confirmAccData = $request->confirmbankaccountnumber;
        // dd( $aadharData );
        $allIssues = ApplicantIncompletDeatil::where('application_id', $realId)->get();

        if ($allIssues->isEmpty()) {
            return true; // No issues found, no need for duplicate check
        }

        foreach ($allIssues as $item) {
            // dd($item);
            $typeCode = $item->incomplet_type ?? null;

            if (in_array($typeCode, ['141', '149', '1414']) && $aadharData) {
                // dd($item->beneficiaryCommonList);
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
    public function incompleteDetails(Request $request)
    {
        $massage = 'Incomplete Details Mis Report';

        $helperData = LgdFilterHelper::getCodesAndInitialCounts($request);

        $masterLocations = $helperData['master_locations'] ?? [];
        $mode = $helperData['mode'] ?? null;
        $col = $helperData['col'] ?? null;
        $name = $helperData['name'] ?? null;
        $blockIds = $helperData['block_ids'] ?? [];
        $subdivisionIds = $helperData['sub_division_ids'] ?? [];

        // Role IDs
        $verifiedRoleId = 1;
        $approvedRoleId = 2;
        $revertRoleId = -50;

        // Build base filters
        $baseFilters = [];
        if (!empty($helperData['district_code'])) {
            $baseFilters['district_id'] = $helperData['district_code'];
        }
        if (!empty($helperData['block_code'])) {
            $baseFilters['block_id'] = $helperData['block_code'];
        }
        if (!empty($helperData['subdivission_code'])) {
            $baseFilters['sub_division_id'] = $helperData['subdivission_code'];
        }
        if (!empty($helperData['rural_urban_code'])) {
            $baseFilters['cd_rural_urban_id'] = $helperData['rural_urban_code'];
        }
        if (!empty($helperData['gpWard_code'])) {
            $baseFilters['cd_gp_ward_id'] = $helperData['gpWard_code'];
        }


        // Initialize location counts
        $locationCounts = [];
        $locationNames = [];
        $columns = $this->getColumnsByMode($mode);

        foreach ($masterLocations as $loc) {
            $key = $loc['location_id'];
            $locationNames[$key] = $loc['location_name'];
            $locationCounts[$key] = [
                'location_name' => $loc['location_name'],
                'pending' => 0,
                'verified' => 0,
                'approved' => 0,
                'reverted' => 0,
            ];
        }


        if (empty($masterLocations)) {

            return view('incomplet.incompleteDetails', [
                'header'  => $massage,
                'helper'  => $helperData,
                'columns' => $columns,
                'name' => $name,
                'data'    => []
            ]);
        }

        // Build base query
        $baseQuery = $this->buildBaseQuery($baseFilters);

        if ($mode === 'block_subdivision') {

            // Extract block/subdivision IDs
            if (empty($blockIds) && empty($subdivisionIds)) {
                foreach ($masterLocations as $loc) {
                    $k = $loc['location_id'];
                    if (is_string($k) && str_contains($k, '_')) {
                        [$pref, $id] = explode('_', $k, 2);
                        if ($pref === 'block') $blockIds[] = (int)$id;
                        if ($pref === 'sub') $subdivisionIds[] = (int)$id;
                    }
                }
            }

            $anyBlocks = !empty($blockIds);
            $anySubdivs = !empty($subdivisionIds);

            if (!$anyBlocks && !$anySubdivs) {
                return view('incomplet.incompleteDetails', [
                    'header'  => $massage,
                    'helper'  => $helperData,
                    'columns' => $columns,
                    'name' => $name,
                    'data'    => []
                ]);
            }

            foreach ($blockIds as $blockId) {
                $key = 'block_' . $blockId;

                if (!isset($locationCounts[$key])) {
                    $locationCounts[$key] = [
                        'location_name' => $locationNames[$key] ?? "Block {$blockId}",
                        'pending' => 0,
                        'verified' => 0,
                        'approved' => 0,
                        'reverted' => 0,
                    ];
                }

                $query = (clone $baseQuery)->where('block_id', $blockId);
                $total = $query->count();

                $locationCounts[$key]['pending'] = $this->countByRoleIdPending(clone $query);
                $locationCounts[$key]['verified'] = $this->countByRoleId((clone $query), $verifiedRoleId);
                $locationCounts[$key]['approved'] = $this->countByRoleId((clone $query), $approvedRoleId);
                $locationCounts[$key]['reverted'] = $this->countByRoleId((clone $query), $revertRoleId);
            }

            // Process subdivisions
            foreach ($subdivisionIds as $subId) {
                $key = 'sub_' . $subId;

                if (!isset($locationCounts[$key])) {
                    $locationCounts[$key] = [
                        'location_name' => $locationNames[$key] ?? "Subdivision {$subId}",
                        'pending' => 0,
                        'verified' => 0,
                        'approved' => 0,
                        'rejected' => 0,
                        'reverted' => 0,
                    ];
                }

                $query = (clone $baseQuery)->where('sub_division_id', $subId);
                $total = $query->count();

                $locationCounts[$key]['pending'] = $this->countByRoleIdPending(clone $query);
                $locationCounts[$key]['verified'] = $this->countByRoleId((clone $query), $verifiedRoleId);
                $locationCounts[$key]['approved'] = $this->countByRoleId((clone $query), $approvedRoleId);
                $locationCounts[$key]['reverted'] = $this->countByRoleId((clone $query), $revertRoleId);
            }
        } else {

            // Normal modes
            if (empty($col)) {
                $col = 'district_id';
            }
            $ids = [];
            foreach ($masterLocations as $loc) {
                if (is_numeric($loc['location_id'])) {
                    $ids[] = (int)$loc['location_id'];
                }
            }
            if (empty($ids)) {
                return view('incomplet.incompleteDetails', [
                    'header'  => $massage,
                    'helper'  => $helperData,
                    'columns' => $columns,
                    'name' => $name,
                    'data'    => []
                ]);
            }

            foreach ($ids as $locId) {
                $locKey = (string)$locId;
                if (!isset($locationCounts[$locKey]) && isset($locationCounts[(int)$locId])) {
                    $locKey = (int)$locId;
                }

                if (!isset($locationCounts[$locKey])) {
                    $locationCounts[$locKey] = [
                        'location_name' => $locationNames[$locKey] ?? $locKey,
                        'pending' => 0,
                        'verified' => 0,
                        'approved' => 0,
                        'reverted' => 0,
                    ];
                }

                $query = (clone $baseQuery)->where($col, $locId);
                $total = $query->count();

                $locationCounts[$locKey]['pending'] = $this->countByRoleIdPending(clone $query);
                $locationCounts[$locKey]['verified'] = $this->countByRoleId((clone $query), $verifiedRoleId);
                $locationCounts[$locKey]['approved'] = $this->countByRoleId((clone $query), $approvedRoleId);
                $locationCounts[$locKey]['reverted'] = $this->countByRoleId((clone $query), $revertRoleId);
            }
        }

        // Ensure integers
        foreach ($locationCounts as &$counts) {
            $counts['pending'] = (int)($counts['pending'] ?? 0);
            $counts['verified'] = (int)($counts['verified'] ?? 0);
            $counts['approved'] = (int)($counts['approved'] ?? 0);
            $counts['reverted'] = (int)($counts['reverted'] ?? 0);
        }

        $data = [];
        foreach ($locationCounts as $key => $row) {
            $pending  = (int)($row['pending'] ?? 0);
            $verified = (int)($row['verified'] ?? 0);
            $approved = (int)($row['approved'] ?? 0);
            $reverted = (int)($row['reverted'] ?? 0);
            $total = $pending + $verified + $approved + $reverted;

            $data[] = [
                'location_name' => $row['location_name'] ?? $key,
                'pending' => $pending,
                'verified' => $verified,
                'approved' => $approved,
                'reverted' => $reverted,
                'total' => $total,
            ];
        }

        return view('incomplet.incompleteDetails', [

            'header' => $massage,
            'helper' => $helperData,
            'columns' => $columns,
            'data' => $data,
            'name' => $name,

        ]);
    }
    private function getColumnsByMode(?string $mode,): array
    {
        // Default location label
        $locationLabel = match ($mode) {
            'block_subdivision' => 'Block / Subdivision',
            'district' => 'District',
            'block' => 'Block',
            'subdivision' => 'Subdivision',
            'gp_ward' => 'GP / Ward',
            'municipality' => 'Municipality',
            'ward' => 'Ward',
            default => 'Location'
        };

        return [
            ['key' => 'location_name', 'label' => $locationLabel, 'align' => 'left', 'type' => 'text'],
            ['key' => 'pending', 'label' => 'Pending verification', 'align' => 'right', 'type' => 'number', 'show_total' => true],
            ['key' => 'verified', 'label' => 'Verified', 'align' => 'right', 'type' => 'number', 'show_total' => true],
            ['key' => 'approved', 'label' => 'Approved', 'align' => 'right', 'type' => 'number', 'show_total' => true],
            // ['key' => 'rejected', 'label' => 'Rejected', 'align' => 'right', 'type' => 'number', 'show_total' => true],
            ['key' => 'reverted', 'label' => 'Reverted', 'align' => 'right', 'type' => 'number', 'show_total' => true],
            ['key' => 'total', 'label' => 'Total', 'align' => 'right', 'type' => 'number', 'show_total' => true],
        ];
    }
    private function buildBaseQuery(array $baseFilters)
    {
        $query = BeneficiaryCommonList::query();

        foreach ($baseFilters as $column => $value) {
            $query->where($column, $value);
        }

        return $query;
    }
    private function countByRoleId($query, $roleId): int
    {
        return (clone $query)
            ->whereHas('applicantIncomplete', function ($q) use ($roleId) {
                $q->where('next_level_request_id', $roleId);
            })
            ->count();
    }
    private function countByRoleIdPending($query): int
    {
        return (clone $query)
            ->whereHas('applicantIncomplete', function ($q) {
                $q->whereNull('next_level_request_id');
            })
            ->count();
    }
}
