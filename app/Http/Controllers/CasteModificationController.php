<?php

namespace App\Http\Controllers;

use App\Helpers\CheckAuthHelper;
use App\Helpers\FormOptionHelper;
use App\Helpers\WorkFlowPermissionHelper;
use App\Models\AcceptRejectInfo;
use App\Models\BeneficiaryPersonalDetail;
use App\Models\BeneficiaryTemEnclosure;
use App\Models\CasteModificationInfo;
use App\Models\Codemaster;
use App\Models\Scheme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class CasteModificationController extends Controller
{
    protected $casteCodeMaster;

    protected $doctype;

    protected $isAuthorized = false;

    protected $accessType;

    public function __construct(Request $request)
    {
        $this->casteCodeMaster = 17;
        $this->doctype = [Codemaster::getIdByCode(162)];

        $currentRoute = $request->route() ? $request->route()->getName() : '';

        $operatorRoutes = [
            'Caste-modification-info',
            'Caste-modification-editview',
            'Caste-modification-update',
        ];

        $verifierRoutes = [
            'Caste-modification-list',
            'Caste-modification-view-details',
        ];

        if (in_array($currentRoute, $operatorRoutes)) {
            if (CheckAuthHelper::isCommonOperator()) {
                $this->isAuthorized = true;
            } else {
                redirect()->route('dashboard')
                    ->with('error', 'Oops! You are not authorized to perform this action.')
                    ->send();
            }
        }

        if (in_array($currentRoute, $verifierRoutes)) {
            if (CheckAuthHelper::isCommonWorkFlow2ndStep()) {
                $this->isAuthorized = true;
            } else {
                redirect()->route('dashboard')
                    ->with('error', 'Oops! You are not authorized to perform this action.')
                    ->send();
            }
        }
    }

    public function index()
    {
        if (WorkFlowPermissionHelper::canModifyCaste()) {
            $header = 'Caste Modification Information';

            return view('CasteModificationView.caste_modification_index', compact('header'));
        }
        $header = 'Oops! You do not have permission to modify caste.';

        return view('CommonRestictedpage.index', compact('header'));
    }

    public function editview(Request $request)
    {
        if (WorkFlowPermissionHelper::canEditCaste()) {
            $header = 'Caste Modification Details';
            $application_id = Crypt::decryptString($request->application_id);
            $beneficiary_id = Crypt::decryptString($request->beneficiary_id);
            $scheme_id = Crypt::decryptString($request->scheme_id);
            $schemeName = Scheme::where('id', $scheme_id)->firstOrFail()->name;
            $reportType = 3;
            $doctype = $this->doctype;
            $BenDetails = BeneficiaryPersonalDetail::where('application_id', $application_id)->where('scheme_id', $scheme_id)->firstOrFail();
            $allCastes = FormOptionHelper::get('Caste');
            $currentCasteId = $BenDetails->caste;
            $castes = collect($allCastes)->filter(fn ($name, $id) => $id != $currentCasteId);
            $checkapplication = CasteModificationInfo::where('application_id', $application_id)->first();
            $isReverted = false;
            $oldData = [];
            if ($checkapplication) {
                $isReverted = $checkapplication->next_level_requested_id == Codemaster::getIdByCode(2204);
                if ($isReverted) {
                    $oldData = $checkapplication->new_data;
                }
            }

            return view('CasteModificationView.beneficiary_cast_edit', compact(
                'application_id',
                'beneficiary_id',
                'scheme_id',
                'schemeName',
                'header',
                'castes',
                'doctype',
                'isReverted',
                'oldData',
                'reportType'
            ));
        }

        $header = 'Oops! You do not have permission to edit caste.';

        return view('CommonRestictedpage.index', compact('header'));
    }

    public function updateCaste(Request $request)
    {
        if (WorkFlowPermissionHelper::canUpdateCaste()) {
            if (! Auth::check()) {
                return redirect()->route('login')->with('error', 'Please login first!');
            }
            $userId = Auth::id();
            $application_id = Crypt::decryptString($request->application_id);
            $scheme_id = Crypt::decryptString($request->scheme_id);

            $rules = [
                'scheme_id' => 'required',
                'application_id' => 'required|string',
                'caste' => 'required|integer|exists:codemasters,id',
                'cast_no' => ['nullable', 'string', 'required_if:caste,1,2'],
            ];

            $messages = [
                'scheme_id.required' => 'Invalid scheme.',
                'application_id.required' => 'Invalid application.',
                'caste.required' => 'Please select a caste.',
                'cast_no.required_if' => 'Caste certificate number is required.',
            ];
            if ($application_id !== null) {
                $uploadedDocsCount = BeneficiaryTemEnclosure::where('application_id', $application_id)->where('scheme_id', $scheme_id)
                    ->whereIn('document_type', $this->doctype)
                    ->count();

                $casteValue = $request->input('caste');
                if (in_array($casteValue, ['1', '2', 1, 2], true) && $uploadedDocsCount < 1) {
                    $rules['document_upload'] = 'required';
                    $messages['document_upload.required'] = 'Please upload the required document.';
                }
            }
            $request->validate($rules, $messages);

            $beneficiary = BeneficiaryPersonalDetail::where('application_id', $application_id)->where('scheme_id', $scheme_id)->firstOrFail();
            $oldData = [
                'caste' => $beneficiary->caste,
                'caste_certificate_no' => $beneficiary->caste_cer_no,
            ];
            $newData = [
                'caste' => $request->caste,
                'caste_certificate_no' => $request->cast_no,
            ];
            $previousId = AcceptRejectInfo::where('application_id', $application_id)
                ->orderByDesc('id')
                ->value('id');
            $existingModification = CasteModificationInfo::where('application_id', $application_id)
                ->where('next_level_requested_id', Codemaster::getIdByCode(2204))
                ->where('is_active', true)
                ->first();
            DB::beginTransaction();
            try {
                if ($existingModification) {
                    $acceptReject = new AcceptRejectInfo;
                    $acceptReject->application_id = $application_id;
                    $acceptReject->beneficiary_id = $existingModification->beneficiary_id;
                    $acceptReject->ip_address = request()->ip();
                    $acceptReject->user_id = Auth::id();
                    $acceptReject->browser = request()->header('User-Agent');
                    $acceptReject->model_name = request()->path();
                    $acceptReject->op_type = Codemaster::getIdByCode(2107);
                    $acceptReject->scheme_id = $scheme_id;
                    $acceptReject->revert_reason_cause_id = null;
                    $acceptReject->revert_reason_remarks = null;
                    $acceptReject->parent_id = $previousId;
                    $acceptSaved = $acceptReject->save();

                    $existingModification->new_data = $newData;
                    $existingModification->caste_request_type = $request->caste;
                    $existingModification->next_level_requested_id = Codemaster::getIdByCode(2201);
                    $existingModification->updated_by = $userId;
                    $existingModification->request_id = $acceptReject->id;
                    $updatedModification = $existingModification->save();
                    if ($acceptSaved && $updatedModification) {
                        DB::commit();

                        return redirect()->route('caste-modification-list', ['retain_filters' => 1])
                            ->with('success', 'Caste re-apply request sent successfully!');
                    }
                    DB::rollBack();

                    return back()->with('error', 'Something went wrong.');
                } else {
                    $logdetails = new AcceptRejectInfo;
                    $logdetails->application_id = $beneficiary->application_id;
                    $logdetails->beneficiary_id = $beneficiary->beneficiary_id;
                    $logdetails->ip_address = request()->ip();
                    $logdetails->user_id = $userId;
                    $logdetails->browser = request()->header('User-Agent');
                    $logdetails->model_name = request()->path();
                    $logdetails->scheme_id = $scheme_id;
                    $logdetails->op_type = Codemaster::getIdByCode(2107);
                    $logdetails->save();

                    $modified_caste = new CasteModificationInfo;
                    $modified_caste->application_id = $beneficiary->application_id;
                    $modified_caste->beneficiary_id = $beneficiary->beneficiary_id;
                    $modified_caste->scheme_id = $scheme_id;
                    $modified_caste->old_data = $oldData;
                    $modified_caste->new_data = $newData;
                    $modified_caste->caste_request_type = $request->caste;
                    $modified_caste->next_level_requested_id = Codemaster::getIdByCode(2201);
                    $modified_caste->request_id = $logdetails->id;
                    $modified_caste->created_by = $userId;
                    $modified_caste->save();

                    DB::commit();

                    return redirect()->route('Caste-modification-info')
                        ->with('success', 'Caste updated request processed successfully!');
                }
            } catch (\Exception $e) {
                dd($e);
                DB::rollBack();

                return back()->with('error', 'Something went wrong: '.$e->getMessage());
            }
        }
        $header = 'Oops! You do not have permission to update caste.';

        return view('CommonRestictedpage.index', compact('header'));
    }

    public function list()
    {
        if (WorkFlowPermissionHelper::canCasteModification()) {
            $header = 'Caste Modification Information List';

            return view('CasteModificationView.caste_modification_list', compact('header'));
        }
        $header = 'Oops! You do not have permission to view caste modification list.';

        return view('CommonRestictedpage.index', compact('header'));
    }

    public function viewAppDetails(Request $request)
    {
        if (WorkFlowPermissionHelper::canBeneficiaryDetails()) {
            $applicant_id = trim($request->application_id);
            $scheme_raw = trim($request->Scheme);
            $application_id = Crypt::decryptString($applicant_id);
            $scheme_id = Crypt::decryptString($scheme_raw);
            $schemeName = Scheme::where('id', $scheme_id)->firstOrFail()->name;

            $application = CasteModificationInfo::where('application_id', $application_id)->where('is_active', true)->firstOrFail();
            $oldData = $application->old_data;
            $newData = $application->new_data;

            $oldCasteName = FormOptionHelper::label('Caste', $oldData['caste']) ?? 'N/A';
            $newCasteName = FormOptionHelper::label('Caste', $newData['caste']) ?? 'N/A';
            $oldCasteNumber = $oldData['caste_certificate_no'] ?? 'N/A';
            $newCasteNumber = $newData['caste_certificate_no'] ?? 'N/A';
            $reportType = 3;

            $header = 'Application Details';

            return view('CasteModificationView.beneficiary_details', compact(
                'header',
                'application_id',
                'application',
                'oldCasteName',
                'newCasteName',
                'oldCasteNumber',
                'newCasteNumber',
                'reportType',
                'scheme_id',
                'schemeName'
            ));
        }

        $header = 'Oops! You do not have permission to view beneficiary details.';

        return view('CommonRestictedpage.index', compact('header'));
    }
}
