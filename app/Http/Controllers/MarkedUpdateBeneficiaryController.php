<?php

namespace App\Http\Controllers;

use App\Helpers\CheckAuthHelper;
use App\Models\AcceptRejectInfo;
use App\Models\BeneficiaryCommonList;
use App\Models\BeneficiaryModificationAllowed;
use App\Models\ChangeTypeMaster;
use App\Models\Codemaster;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class MarkedUpdateBeneficiaryController extends Controller
{
    public function index()
    {
        if (CheckAuthHelper::isHOD()) {
            $header = 'Marked Beneficiary To Update Informations';
            return view('MarkedUpdateBeneficiary.index', compact('header'));
        } else {
            $header = 'Opps! you are not able to perform any action';
            return view('CommonRestictedpage\index', compact('header'));
        }
    }
    public function editview(Request $request)
    {
        if (CheckAuthHelper::isHOD()) {
            $header = 'Mark Beneficiary to modify Details';
            $application_id = Crypt::decryptString($request->application_id);
            $beneficiary_id = Crypt::decryptString($request->beneficiary_id);
            $reportType = 3;
            $marked_allowed_field = ChangeTypeMaster::where('is_active', 1)->get();
            // dd($marked_allowed_field);
            $sectionType = 0;
            // dd($BenDetails);
            return view('MarkedUpdateBeneficiary.editview', compact('header', 'application_id', 'beneficiary_id', 'reportType', 'marked_allowed_field', 'sectionType'));
        } else {
            $header = 'Opps! you are not able to perform any action';
            return view('CommonRestictedpage\index', compact('header'));
        }
    }
    public function marked(Request $request)
    {
        // 150000017
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login first!');
        }
        $userId = Auth::id();
        $applicationId = Crypt::decryptString($request->application_id);
        $beneficiaryId = Crypt::decryptString($request->beneficiary_id);
        $markedFields = (array) $request->marked_fields;
        $request->validate([
            'application_id' => 'required',
        ]);
        $fields = ChangeTypeMaster::whereIn('id', $markedFields)
            ->where('is_active', 1)
            ->get(['id', 'name', 'short_name', 'code']);
        // dd($fields);
        $allowedFields = $fields->map(fn($f) => [
            'id'         => $f->id,
            'code'       => $f->code,
            'short_name' => $f->short_name,
        ])->values();
        // dd($allowedFields);
        $oldValue = $fields->pluck('name', 'short_name');
        // dd($oldValue);
        $beneficiary = BeneficiaryCommonList::where('sourceable_id', $applicationId)->with('sourceable')->firstOrFail();
        $markedField = BeneficiaryModificationAllowed::where('application_id', $applicationId)->where('is_active', true)->first();
        if ($markedField) {
            return redirect()->route('marked-beneficiary')->with('success', 'Beneficiary  Already Marked for Update!');
        } else {
            try {
                DB::beginTransaction();
                $logdetails = new AcceptRejectInfo;
                $logdetails->application_id = $beneficiary->sourceable_id;
                $logdetails->beneficiary_id = $beneficiary->beneficiary_id;
                $logdetails->old_value = json_encode($oldValue);
                $logdetails->ip_address = request()->ip();
                $logdetails->user_id = $userId;
                $logdetails->browser = request()->header('User-Agent');
                $logdetails->model_name = request()->path();
                $logdetails->op_type = Codemaster::getIdByCode(2110);
                $logInfo = $logdetails->save();

                $MarkedBeneficiary = new BeneficiaryModificationAllowed;
                $MarkedBeneficiary->application_id = $applicationId;
                $MarkedBeneficiary->beneficiary_id = $beneficiaryId;
                $MarkedBeneficiary->allowed_fields = $allowedFields;
                $MarkedBeneficiary->allowed_by = $userId;
                $MarkedBeneficiary->updated_by = $userId;
                $MarkedBeneficiaryfields = $MarkedBeneficiary->save();
                if ($logInfo && $MarkedBeneficiaryfields) {
                    DB::commit();
                    return redirect()->route('marked-beneficiary')->with('success', 'Beneficiary Marked for Update Successfully!');
                } else {
                    DB::rollBack();
                    return redirect()->route('marked-beneficiary')->with('error', 'Something went wrong!');
                }
            } catch (\Exception $e) {
                dd($e);
                DB::rollBack();
                return back()->with('error', 'Something went wrong!');
            }
        }
    }
    public function list()
    {
        $header = 'Marked Beneficiary Modification List';
        return view('MarkedUpdateBeneficiary.beneficiary_modification_list', compact('header'));
    }
    public function viewmarkedbeneficiarydetails(Request $request)
    {
        $header = 'Marked Beneficiary Details View';
        $applicant_id   = $request->application_id;
        $application_id = Crypt::decryptString($applicant_id);
        $reportType  = 3;
        $sectionType = 1;
        $marked = BeneficiaryModificationAllowed::where('application_id', $application_id)->select('allowed_fields')->first();
        // dd($marked);
        // $allowedFields = $marked?->allowed_fields ?? [];

        // if (!is_array($allowedFields)) {
        //     $allowedFields = json_decode($allowedFields, true) ?? [];
        // }

        // $visibilityMap = [
        //     0 => 'is_name_visible',
        //     1 => 'is_dob_visible',
        //     2 => 'is_address_visible',
        //     3 => 'is_bank_visible',
        //     4 => 'is_mobile_visible',
        // ];
        // foreach ($visibilityMap as $varName) {
        //     $$varName = 0;
        // }
        // foreach ($allowedFields as $field) {
        //     if (!is_array($field) || !isset($field['code'])) {
        //         continue;
        //     }
        //     $code = (int) $field['code'];
        //     if (isset($visibilityMap[$code])) {
        //         $$visibilityMap[$code] = 1;
        //     }
        //     dd($field); // after loop
        // }

        return view(
            'MarkedUpdateBeneficiary.editview',
            compact(
                'header',
                'application_id',
                'reportType',
                'sectionType',
             
            )
        );
    }
}
