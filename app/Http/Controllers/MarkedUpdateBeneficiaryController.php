<?php

namespace App\Http\Controllers;

use App\Helpers\CheckAuthHelper;
use App\Models\AcceptRejectInfo;
use App\Models\BeneficiaryAadhaar;
use App\Models\BeneficiaryCommonList;
use App\Models\BeneficiaryContact;
use App\Models\BeneficiaryModificationAllowed;
use App\Models\BeneficiaryPersonal;
use App\Models\ChangeTypeMaster;
use App\Models\Codemaster;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class MarkedUpdateBeneficiaryController extends Controller
{
    protected $currentDate;
    public function __construct()
    {
       $this->currentDate = Carbon::now()->format('d-m-Y');
            //   dd($this->currentDate);
    }
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
        $currentDate = $this->currentDate;
        $header = 'Marked Beneficiary Details View';
        $applicant_id   = $request->application_id;
        $application_id = Crypt::decryptString($applicant_id);
        $reportType  = 3;

        $sectionType = 1;
        $marked = BeneficiaryModificationAllowed::where('application_id', $application_id)->select('allowed_fields')->first();
        $beneficiarypersonal = BeneficiaryPersonal::where('application_id', $application_id)->select('full_name', 'dob', 'mobile_no')->first();
        $beneficiaryname = $beneficiarypersonal->name;

        $beneficiarycontact = BeneficiaryContact::where('application_id', $application_id)->select('police_station', 'village_town_city', 'house_premise_no', 'post_office', 'pincode', 'district_id', 'rural_urban_id', 'block_id', 'panchayat_id', 'municipality_id', 'ward_id')->first();
        $beneficiaryAadharDetails = BeneficiaryAadhaar::where('application_id', $application_id)->select('encoded_aadhar')->first();
        // dump($beneficiaryAadharDetails);
        // dd($beneficiarypersonal);
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
        $beneficiaryname = $beneficiarypersonal->full_name;
        $beneficiarydob = $beneficiarypersonal->dob;
        $beneficiarymobile = $beneficiarypersonal->mobile_no;
        $beneficiaryaadhar = $beneficiaryAadharDetails->encoded_aadhar;
        if ($beneficiaryaadhar) {
            $beneficiaryaadhar = Crypt::decryptString($beneficiaryaadhar);
            // $beneficiaryaadhar = '**** **** ' . substr(Crypt::decryptString($beneficiaryaadhar), -4);
        } else {
            $beneficiaryaadhar = '';
        }
        // dd($beneficiaryaadhar);
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
                'pincode',
                'beneficiaryname',
                'beneficiarydob',
                'beneficiarymobile',
                'beneficiaryaadhar',
                'currentDate'
            )
        );
    }
    // public function updatemarkedbeneficiarydetails(Request $request)
    // {
    //     dd('ok');
    //     return redirect()->route('marked-beneficiary-list')->with('success', 'Beneficiary Details Updated Successfully!');
    // }

    public function updatemarkedbeneficiarydetails(Request $request)
    {

        // dd($request->all());
        // $application_id = Crypt::decryptString($request->application_id);
        // $allowedupdatedfields = BeneficiaryModificationAllowed::where('application_id', $application_id)->first();
        // dd($allowedupdatedfields);

        $application_id = Crypt::decryptString($request->application_id);

        /* ---------------- RE-FETCH ALLOWED FIELDS ---------------- */
        $allowedJson = BeneficiaryModificationAllowed::where('application_id', $application_id)
            ->value('allowed_fields');
        $allowedFields = is_array($allowedJson)
            ? $allowedJson
            : json_decode($allowedJson, true) ?? [];
        if (isset($allowedFields['allowed_fields'])) {
            $allowedFields = $allowedFields['allowed_fields'];
        }

        /* Flag map */
        $flagMap = [
            0 => 'visible_Name',
            1 => 'visible_DOB',
            2 => 'visible_Address',
            3 => 'visible_Aadhar',
            4 => 'visible_Mobile',
        ];

        /* Initialize flags */
        $visible_Name = $visible_DOB = $visible_Address = $visible_Aadhar = $visible_Mobile = 0;

        /* Build flags */
        foreach ($allowedFields as $field) {
            if (!isset($field['code'])) {
                continue;
            }
            $code = (int) $field['code'];
            if (isset($flagMap[$code])) {
                ${$flagMap[$code]} = 1;
            }
        }
        /* DEBUG ONCE */
        // dd([
        //     'normalized_allowedFields' => $allowedFields,
        //     'visible_Name' => $visible_Name,
        //     'visible_DOB' => $visible_DOB,
        //     'visible_Address' => $visible_Address,
        //     'visible_Aadhar' => $visible_Aadhar,
        //     'visible_Mobile' => $visible_Mobile,
        // ]);
        $rules = [];
        $messages = [];
        if ($visible_Name == 1) {
            $rules['name'] = 'required|string|max:150';
        }
        if ($visible_DOB == 1) {
            $rules['dob'] = 'required|date|before:today';
        }
        if ($visible_Mobile == 1) {
            $rules['mobile'] = 'required|digits:10';
        }
        if ($visible_Address == 1) {

            $rules['policestation'] = 'required|string|max:100';
            $rules['villtowncity']  = 'required|string|max:100';
            $rules['postoffice']    = 'required|string|max:100';
            $rules['pincode']       = 'required|digits:6';

            $rules['district_id']    = 'required|integer';
            $rules['rural_urban'] = 'required|in:1,2';

            if ($request->rural_urban_id == 2) {
                $rules['block_id']     = 'required|integer';
                $rules['panchayat_id'] = 'required|integer';
            }
            if ($request->rural_urban_id == 1) {
                $rules['municipality_id'] = 'required|integer';
                $rules['ward_id']         = 'required|integer';
            }
        }

        // ---- AADHAAR ----
        // if ($visible_Aadhar == 1) {
        //     $rules['aadhaar'] = 'required|digits:12';
        // }
        if (!empty($rules)) {
            // try {
                $request->validate($rules, $messages);
            // } catch (\Illuminate\Validation\ValidationException $e) {
            //     dd([
            //         'VALIDATION FAILED',
            //         'errors' => $e->errors(),
            //         'rules'  => $rules,
            //         'input'  => $request->all(),
            //     ]);
            // }
        }
        // dd('sfsf55');
        // dd($request->validate($rules, $messages));
        DB::beginTransaction();
        try {

            /* ---------- PERSONAL ---------- */
            $personalData = [];

            if ($visible_Name == 1) {
                $personalData['full_name'] = $request->name;
            }

            if ($visible_DOB == 1) {
                $personalData['dob'] = $request->dob;
            }

            if ($visible_Mobile == 1) {
                $personalData['mobile_no'] = $request->mobile;
            }

            if (!empty($personalData)) {
                BeneficiaryPersonal::updateOrCreate(
                    ['application_id' => $application_id],
                    $personalData
                );
            }
            if ($visible_Address == 1) {
                $contactData = [
                    'police_station'     => $request->policestation,
                    'village_town_city' => $request->villtowncity,
                    'house_premise_no'  => $request->housepremiseno,
                    'post_office'       => $request->postoffice,
                    'pincode'           => $request->pincode,
                    'district_id'       => $request->district_id,
                    'rural_urban_id'    => $request->rural_urban_id,
                    'block_id'          => null,
                    'panchayat_id'      => null,
                    'municipality_id'   => null,
                    'ward_id'           => null,
                ];
                if ((int) $request->rural_urban_id === 2) {
                    // Rural
                    $contactData['block_id']     = $request->block_id;
                    $contactData['panchayat_id'] = $request->panchayat_id;
                }
                if ((int) $request->rural_urban_id === 1) {
                    // Urban
                    $contactData['municipality_id'] = $request->municipality_id;
                    $contactData['ward_id']         = $request->ward_id;
                }
                BeneficiaryContact::updateOrCreate(
                    ['application_id' => $application_id],
                    $contactData
                );
            }
            // if ($visible_Aadhar == 1) {
            //     BeneficiaryAadhaar::updateOrCreate(
            //         ['application_id' => $application_id],
            //         [
            //             'encoded_aadhar' => Crypt::encryptString($request->aadhaar),
            //             'aadhar_hash'    => md5($request->aadhaar),
            //         ]
            //     );
            // }

            DB::commit();

            return redirect()
                ->route('marked-beneficiary-list')
                ->with('success', 'Beneficiary Details Updated Successfully');
        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with('error', 'Update failed. Please try again.');
        }
    }
}
