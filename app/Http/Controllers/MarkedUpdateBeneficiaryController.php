<?php

namespace App\Http\Controllers;

use App\Helpers\CheckAuthHelper;
use App\Models\AcceptRejectInfo;
use App\Models\BeneficiaryCommonList;
use App\Models\BeneficiaryContact;
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
        $oldValue = $fields->pluck('name')->values();

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
        $beneficiarycontact = BeneficiaryContact::where('application_id', $application_id)->select('police_station', 'village_town_city', 'house_premise_no', 'post_office', 'pincode', 'district_id', 'rural_urban_id', 'block_id', 'panchayat_id', 'municipality_id', 'ward_id')->first();
        // dd($beneficiarycontact);
        $selectedDistrict  = $beneficiarycontact->district_id;
        $selectedRuralurban = $beneficiarycontact->rural_urban_id;
        if (($selectedRuralurban) == 2) {
            // dump('ok1');
            $selectedBlockurban = $beneficiarycontact->block_id;
            $selectedGpWard = $beneficiarycontact->panchayat_id;
            // dd($this->selectedGpWard);
        } else {
            // dd('dcd');
            $selectedBlockurban = $beneficiarycontact->municipality_id;
            $selectedGpWard = $beneficiarycontact->ward_id;
        }
        $policestation = $beneficiarycontact->police_station;
        $villtowncity = $beneficiarycontact->village_town_city;
        $housepremiseno = $beneficiarycontact->house_premise_no;
        $postoffice = $beneficiarycontact->post_office;
        $pincode = $beneficiarycontact->pincode;

        // dd($marked);
        $allowedFields = $marked?->allowed_fields ?? [];
        // dd($allowedFields);
        $allowedFields = $marked?->allowed_fields ?? [];
        if (!is_array($allowedFields)) {
            $allowedFields = json_decode($allowedFields, true) ?? [];
        }

        $flagMap = [
            0 => 'visible_Name',
            1 => 'visible_DOB',
            2 => 'visible_Address',
            3 => 'visible_Aadhar',
            4 => 'visible_Mobile',
        ];
        foreach ($flagMap as $flagVar) {
            $$flagVar = 0;
        }
        foreach ($allowedFields as $field) {
            if (!is_array($field) || !isset($field['code'])) {
                continue;
            }

            $code = (int) $field['code'];

            if (isset($flagMap[$code])) {
                ${$flagMap[$code]} = 1;
            }
        }
        // dump([
        //     'code' => $code,
        //     'mapped_flag' => $flagMap[$code],
        // ]);
        // dd([
        //     'visible_Name'    => $visible_Name,
        //     'visible_DOB'     => $visible_DOB,
        //     'visible_Address' => $visible_Address,
        //     'visible_Bank'    => $visible_Bank,
        //     'visible_Mobile'  => $visible_Mobile,
        // ]);

        return view(
            'MarkedUpdateBeneficiary.editview',
            compact(
                'header',
                'application_id',
                'reportType',
                'sectionType',
                'visible_Name',
                'visible_DOB',
                'visible_Address',
                'visible_Aadhar',
                'visible_Mobile',
                'selectedDistrict',
                'selectedRuralurban',
                'selectedBlockurban',
                'selectedGpWard',
                'policestation',
                'villtowncity',
                'housepremiseno',
                'postoffice',
                'pincode'
            )
        );
    }
    public function updatemarkedbeneficiarydetails(Request $request)
    {
        dd('ok');
        return redirect()->route('marked-beneficiary-list')->with('success', 'Beneficiary Details Updated Successfully!');
    }
}
