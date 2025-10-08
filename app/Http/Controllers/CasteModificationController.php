<?php

namespace App\Http\Controllers;

use App\Models\AcceptRejectInfo;
use App\Models\BeneficiaryCommonList;
use App\Models\CasteModificationInfo;
use App\Models\Codemaster;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class CasteModificationController extends Controller
{
    protected $casteCodeMaster;
    protected $doctype;
    public function __construct()
    {
        $this->casteCodeMaster = 17;
        $this->doctype = [101];
    }

    public function index()
    {
        // $user = auth()->user();
        // $user->hasRole('Operator');

        // dd('caste modification info');
        // if ($user->hasRole('Operator')) {}
        $header = 'Caste Modification Information';
        return view('CasteModificationView.caste_modification_index', compact('header'));
    }
    public function editview(Request $request)
    {
        // dd($request->all());
        $header = 'Caste Modification Details';
        $application_id = Crypt::decryptString($request->application_id);
        $beneficiary_id = Crypt::decryptString($request->beneficiary_id);
        $caste_field_visible = 0;
        // $casteid=Codemaster::getIdByCode($this->casteCodeMaster);
        // dd($casteParentid);
        // dd($application_id);
        $reportType=3;
        $doctype = $this->doctype;
        $BenDetails = BeneficiaryCommonList::where('sourceable_id', $application_id)->with('sourceable')->firstOrFail();
        // dd($BenDetails);
        $allCastes = Codemaster::where('code', $this->casteCodeMaster)
            ->first()
            ->children()
            ->pluck('name', 'id');
        // dd($allCastes);
        $currentCasteId = $BenDetails->sourceable->caste;
        // dd($currentCasteId);
        $castes = $allCastes->filter(fn($name, $id) => $id != $currentCasteId);
        // dd($castes);
        $checkapplication = CasteModificationInfo::where('application_id', $application_id)->first();
// dd($checkapplication);
        $isReverted = false;
        $oldData = [];
        if ($checkapplication) {
            $isReverted = $checkapplication->next_level_requested_id == Codemaster::getIdByCode(2204);
            if ($isReverted) {
                $oldData = $checkapplication->new_data;
            }
        }
        // dd($castes);
        // dump($isReverted);
        // dd( $oldData);
        return view('CasteModificationView.beneficiary_cast_edit', compact('application_id', 'header', 'castes', 'doctype', 'isReverted', 'oldData','reportType'));
    }

    public function updateCaste(Request $request)
    {
        // dd('njhn');
        // dd($request->all());

        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login first!');
        }
        $userId = Auth::id();

        $request->validate([
            'application_id' => 'required|string',
            'caste' => 'required|integer|exists:codemasters,id',
            'cast_no' => [
                'nullable',
                'string',
                'required_if:caste,17,18',
            ],
        ], [
            // application_id
            'application_id.required' => 'Invalid application.',
            // caste
            'caste.required' => 'Please select a caste.',
            // cast_no
            'cast_no.required_if' => 'Caste certificate number is required .',

        ]);
        $application_id = Crypt::decryptString($request->application_id);
        // dd($application_id);

        $beneficiary = BeneficiaryCommonList::where('sourceable_id', $application_id)->with('sourceable')->firstOrFail();
        // dd($beneficiary);
        if (!$beneficiary) {
            return back()->with('error', 'Beneficiary not found!');
        }
        $oldData = [
            'caste' => $beneficiary->sourceable->caste,
            'caste_certificate_no' => $beneficiary->sourceable->caste_certificate_no,
        ];

        $newData = [
            'caste' => $request->caste,
            'caste_certificate_no' => $request->cast_no,
        ];
        // dump($oldData);
        // dd($newData);
        // DB::beginTransaction();
        // try {

        //     $logdetails = new AcceptRejectInfo;
        //     $logdetails->application_id         = $beneficiary->sourceable_id;
        //     $logdetails->beneficiary_id         = $beneficiary->beneficiary_id;
        //     $logdetails->ip_address             = request()->ip();
        //     $logdetails->user_id                = $userId;
        //     $logdetails->browser                = request()->header('User-Agent');
        //     $logdetails->model_name             = request()->path();
        //     $logdetails->op_type                = Codemaster::getIdByCode(2106);
        //     $logdetails->revert_reason_cause_id = null;
        //     $logdetails->revert_reason_remarks  = null;
        //     $logdetails->parent_id              = null;
        //     // dd($logdetails);
        //     $logdetailsSaved = $logdetails->save();


        //     $modified_caste = new CasteModificationInfo;
        //     $modified_caste->application_id          = $beneficiary->sourceable_id;
        //     $modified_caste->beneficiary_id          = $beneficiary->beneficiary_id;
        //     $modified_caste->old_data                = $oldData;
        //     $modified_caste->new_data                = $newData;
        //     $modified_caste->caste_request_type      = $request->caste;
        //     $modified_caste->next_level_requested_id = Codemaster::getIdByCode(2201);
        //     $modified_caste->request_id              = $logdetails->id;
        //     $modified_caste->created_by              = $userId;
        //     $modified_caste->updated_by              = $userId;
        //     $modifiedCasteSaved = $modified_caste->save();


        //     if ($logdetailsSaved && $modifiedCasteSaved) {
        //         DB::commit();
        //         return redirect()->route('Caste-modification-info')
        //             ->with('success', 'Caste updated Request Process Successfully!');
        //     } else {
        //         DB::rollBack();
        //         return back()->with('error', 'Failed to save caste modification.');
        //     }
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        // }
        $previousId = AcceptRejectInfo::where('application_id', $application_id)
            ->orderByDesc('id')
            ->value('id');
        $existingModification = CasteModificationInfo::where('application_id', $beneficiary->sourceable_id)
            ->where('next_level_requested_id', Codemaster::getIdByCode(2204)) // Rejected state
            ->first();

        DB::beginTransaction();
        try {
            if ($existingModification) {
                $acceptReject = new AcceptRejectInfo();
                $acceptReject->application_id         = $application_id;
                $acceptReject->beneficiary_id         = $existingModification->beneficiary_id;
                $acceptReject->ip_address             = request()->ip();
                $acceptReject->user_id                = Auth::id();
                $acceptReject->browser                = request()->header('User-Agent');
                $acceptReject->model_name             = request()->path();
                $acceptReject->op_type                = Codemaster::getIdByCode(2106);
                $acceptReject->revert_reason_cause_id = null;
                $acceptReject->revert_reason_remarks  = null;
                $acceptReject->parent_id              = $previousId;
                $acceptSaved = $acceptReject->save();

                // Update request_id with new log
                $existingModification->new_data = $newData;
                $existingModification->caste_request_type = $request->caste;
                $existingModification->next_level_requested_id = Codemaster::getIdByCode(2201); // forward again
                $existingModification->updated_by = $userId;
                $existingModification->request_id = $acceptReject->id;
                $updatedModification = $existingModification->save();

                if ($acceptSaved && $updatedModification) {
                    DB::commit();
                    return redirect()->route('Caste-modification-info')
                        ->with('success', 'Caste re-apply request send successfully!');
                } else {
                    DB::rollBack();
                    return back()->with('error', 'Something went wrong: ');
                }
            } else {
                // --- Fresh new request ---
                $logdetails = new AcceptRejectInfo;
                $logdetails->application_id         = $beneficiary->sourceable_id;
                $logdetails->beneficiary_id         = $beneficiary->beneficiary_id;
                $logdetails->ip_address             = request()->ip();
                $logdetails->user_id                = $userId;
                $logdetails->browser                = request()->header('User-Agent');
                $logdetails->model_name             = request()->path();
                $logdetails->op_type                = Codemaster::getIdByCode(2106);
                $logdetails->revert_reason_cause_id = null;
                $logdetails->revert_reason_remarks  = null;
                $logdetails->parent_id              = null;
                // dd($logdetails);
                $logdetailsSaved = $logdetails->save();


                $modified_caste = new CasteModificationInfo;
                $modified_caste->application_id          = $beneficiary->sourceable_id;
                $modified_caste->beneficiary_id          = $beneficiary->beneficiary_id;
                $modified_caste->old_data                = $oldData;
                $modified_caste->new_data                = $newData;
                $modified_caste->caste_request_type      = $request->caste;
                $modified_caste->next_level_requested_id = Codemaster::getIdByCode(2201);
                $modified_caste->request_id              = $logdetails->id;
                $modified_caste->created_by              = $userId;
                $modified_caste->updated_by              = $userId;
                $modifiedCasteSaved = $modified_caste->save();


                if ($logdetailsSaved && $modifiedCasteSaved) {
                    DB::commit();
                    return redirect()->route('Caste-modification-info')
                        ->with('success', 'Caste updated Request processed Successfully!');
                } else {
                    DB::rollBack();
                    return back()->with('error', 'Failed to save caste modification.');
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
    public function list()
    {
        // $user = auth()->user();
        // $user->hasRole('Operator');
        // dd('caste modification info');
        // if ($user->hasRole('Operator')) {}
        $header = 'Caste Modification Information List';
        return view('CasteModificationView.caste_modification_list', compact('header'));
    }
    public function viewAppDetails(Request $request)
    {
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

        // dump($oldCasteName);
        // dd($newCasteName);

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
}
