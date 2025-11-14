<?php

namespace App\Http\Controllers;

use App\Models\AcceptRejectInfo;
use App\Models\BeneficiaryCommonList;
use App\Models\CasteModificationInfo;
use App\Models\Codemaster;
use Illuminate\Http\Request;
use App\Helpers\CheckAuthHelper;
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
            'Caste-modification-update'
        ];

        $verifierRoutes = [
            'Caste-modification-list',
            'Caste-modification-view-details'
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

    /** -------------------- OPERATOR ACCESS -------------------- **/
    public function index()
    {
        if (Auth::user()->can('modify caste')) {
            $header = 'Caste Modification Information';
            return view('CasteModificationView.caste_modification_index', compact('header'));
        }
        $header = 'Oops! You do not have permission to modify caste.';
        return view('CommonRestictedpage.index', compact('header'));
    }

    public function editview(Request $request)
    {
        if (Auth::user()->can('edit caste')) {
            $header = 'Caste Modification Details';
            $application_id = Crypt::decryptString($request->application_id);
            $beneficiary_id = Crypt::decryptString($request->beneficiary_id);
            $reportType = 3;
            $doctype = $this->doctype;

            $BenDetails = BeneficiaryCommonList::where('sourceable_id', $application_id)->with('sourceable')->firstOrFail();

            $allCastes = Codemaster::where('code', $this->casteCodeMaster)
                ->first()
                ->children()
                ->pluck('name', 'id');

            $currentCasteId = $BenDetails->sourceable->caste;
            $castes = $allCastes->filter(fn($name, $id) => $id != $currentCasteId);

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
        if (Auth::user()->can('update caste')) {
            if (!Auth::check()) {
                return redirect()->route('login')->with('error', 'Please login first!');
            }

            $userId = Auth::id();

            $request->validate([
                'application_id' => 'required|string',
                'caste' => 'required|integer|exists:codemasters,id',
                'cast_no' => ['nullable', 'string', 'required_if:caste,17,18'],
            ], [
                'application_id.required' => 'Invalid application.',
                'caste.required' => 'Please select a caste.',
                'cast_no.required_if' => 'Caste certificate number is required.',
            ]);

            $application_id = Crypt::decryptString($request->application_id);
            $beneficiary = BeneficiaryCommonList::where('sourceable_id', $application_id)->with('sourceable')->firstOrFail();

            $oldData = [
                'caste' => $beneficiary->sourceable->caste,
                'caste_certificate_no' => $beneficiary->sourceable->caste_certificate_no,
            ];

            $newData = [
                'caste' => $request->caste,
                'caste_certificate_no' => $request->cast_no,
            ];

            $previousId = AcceptRejectInfo::where('application_id', $application_id)
                ->orderByDesc('id')
                ->value('id');

            $existingModification = CasteModificationInfo::where('application_id', $beneficiary->sourceable_id)
                ->where('next_level_requested_id', Codemaster::getIdByCode(2204))
                ->first();

            DB::beginTransaction();
            try {
                if ($existingModification) {
                    $acceptReject = new AcceptRejectInfo();
                    $acceptReject->application_id = $application_id;
                    $acceptReject->beneficiary_id = $existingModification->beneficiary_id;
                    $acceptReject->ip_address = request()->ip();
                    $acceptReject->user_id = Auth::id();
                    $acceptReject->browser = request()->header('User-Agent');
                    $acceptReject->model_name = request()->path();
                    $acceptReject->op_type = Codemaster::getIdByCode(2106);
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
                        return redirect()->route('Caste-modification-info')
                            ->with('success', 'Caste re-apply request sent successfully!');
                    }

                    DB::rollBack();
                    return back()->with('error', 'Something went wrong.');
                } else {
                    $logdetails = new AcceptRejectInfo;
                    $logdetails->application_id = $beneficiary->sourceable_id;
                    $logdetails->beneficiary_id = $beneficiary->beneficiary_id;
                    $logdetails->ip_address = request()->ip();
                    $logdetails->user_id = $userId;
                    $logdetails->browser = request()->header('User-Agent');
                    $logdetails->model_name = request()->path();
                    $logdetails->op_type = Codemaster::getIdByCode(2106);
                    $logdetails->save();

                    $modified_caste = new CasteModificationInfo;
                    $modified_caste->application_id = $beneficiary->sourceable_id;
                    $modified_caste->beneficiary_id = $beneficiary->beneficiary_id;
                    $modified_caste->old_data = $oldData;
                    $modified_caste->new_data = $newData;
                    $modified_caste->caste_request_type = $request->caste;
                    $modified_caste->next_level_requested_id = Codemaster::getIdByCode(2201);
                    $modified_caste->request_id = $logdetails->id;
                    $modified_caste->created_by = $userId;
                    $modified_caste->updated_by = $userId;
                    $modified_caste->save();

                    DB::commit();
                    return redirect()->route('Caste-modification-info')
                        ->with('success', 'Caste updated request processed successfully!');
                }
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Something went wrong: ' . $e->getMessage());
            }
        }

        $header = 'Oops! You do not have permission to update caste.';
        return view('CommonRestictedpage.index', compact('header'));
    }

    /** -------------------- VERIFIER / APPROVER ACCESS -------------------- **/
    public function list()
    {
        if (Auth::user()->can('view caste modification list')) {
            $header = 'Caste Modification Information List';
            return view('CasteModificationView.caste_modification_list', compact('header'));
        }
        $header = 'Oops! You do not have permission to view caste modification list.';
        return view('CommonRestictedpage.index', compact('header'));
    }

    public function viewAppDetails(Request $request)
    {
        if (Auth::user()->can('view beneficiary details')) {
            $applicant_id = $request->application_id;
            $application_id = Crypt::decrypt($applicant_id);

            $application = CasteModificationInfo::where('application_id', $application_id)->firstOrFail();
            $oldData = $application->old_data;
            $newData = $application->new_data;

            $oldCasteName = Codemaster::find($oldData['caste'])->name ?? 'N/A';
            $newCasteName = Codemaster::find($newData['caste'])->name ?? 'N/A';
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
                'reportType'
            ));
        }

        $header = 'Oops! You do not have permission to view beneficiary details.';
        return view('CommonRestictedpage.index', compact('header'));
    }
}
