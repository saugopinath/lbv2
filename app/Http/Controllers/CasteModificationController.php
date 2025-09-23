<?php

namespace App\Http\Controllers;

use App\Models\AcceptRejectInfo;
use App\Models\BeneficiaryCommonList;
use App\Models\CasteModificationInfo;
use App\Models\Codemaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

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
        $doctype = $this->doctype;

        $BenDetails = BeneficiaryCommonList::where('sourceable_id', $application_id)->with('sourceable')->firstOrFail();
        // dd($BenDetails);
        $allCastes = Codemaster::where('code', $this->casteCodeMaster)
            ->first()
            ->children()
            ->pluck('name', 'id');

        $currentCasteId = $BenDetails->sourceable->caste;
        // dd($currentCasteId);

        $castes = $allCastes->filter(fn($name, $id) => $id != $currentCasteId);
        // dd($castes);

        return view('CasteModificationView.beneficiary_cast_edit', compact('application_id', 'header', 'castes', 'doctype'));
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
        $logdetails = AcceptRejectInfo::create([
            'application_id'         => $beneficiary->sourceable_id,
            'beneficiary_id'         => $beneficiary->beneficiary_id,
            'ip_address'             => request()->ip(),
            'user_id'                => $userId,
            'browser'                => request()->header('User-Agent'),
            'model_name'             => CasteModificationInfo::class,
            'op_type'                => Codemaster::getIdByCode(2106),
            'revert_reason_cause_id' => null,
            'revert_reason_remarks'  => null,
            'parent_id'              => null,
        ]);

        $modified_caste = CasteModificationInfo::create([
            'application_id' => $beneficiary->sourceable_id,
            'beneficiary_id' => $beneficiary->beneficiary_id,
            'old_data' => $oldData,
            'new_data' => $newData,
            'caste_request_type' => $request->caste,
            'next_level_requested_id' => Codemaster::getIdByCode(2201),
            'request_id' => $logdetails->id,
            'created_by' => $userId,
            'updated_by' => $userId,
        ]);
        // dd($modified_caste);

        return redirect()->route('Caste-modification-info')->with('success', 'Caste details updated successfully!');
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
            'newCasteNumber'
        ));
    }
}
