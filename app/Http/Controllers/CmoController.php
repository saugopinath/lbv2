<?php

namespace App\Http\Controllers;

use App\Helpers\CheckAuthHelper;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Interfaces\CmoAuthenticationInterface;
use App\Models\CmoResponseJson;
use App\Models\CmoSmData;
use Illuminate\Support\Collection;
use App\Models\Municipality;
use App\Models\Codemaster;
use Illuminate\Support\Facades\Crypt;
use App\Models\CmoAtrMaster;
use Illuminate\Support\Facades\Auth;
use App\Models\BeneficiaryCommonList;
use App\Models\BeneficiaryPersonalDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CmoController extends Controller
{
    protected $cmoAuthenticationService;

    public function __construct(CmoAuthenticationInterface $cmoAuthenticationService)
    {
        $this->cmoAuthenticationService = $cmoAuthenticationService;
    }

    public function pullnewcmo(Request $request)
    {
        $inserted_id = $request->query('inserted_id');
        if ($request->isMethod('post')) {
            $rules = [
                'from_date' => 'required|date',
                'to_date'   => 'required|date|after_or_equal:from_date',
            ];
            $messages = [
                'from_date.*' => 'Please select a valid start date.',
                'to_date.*'   => 'Please select a valid end date and end date cannot be before of the start date.',
            ];
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }
            DB::beginTransaction();
            try {
                $from_date = $request->from_date;
                $to_date   = $request->to_date;
                $data = $this->cmoAuthenticationService->pullNewCmo($from_date, $to_date);
                $response = json_decode($data->getContent(), true);
                if (isset($response['inserted_id']) && $response['status'] == 200) {
                    $inserted_id = $response['inserted_id'];
                    DB::commit();
                    session()->flash('success', 'Data pulled successfully!');
                    return redirect()->route('pullnewcmo', ['inserted_id' => $inserted_id])->withInput();
                } else {
                    DB::rollBack();
                    session()->flash('error', 'Failed to pull data.');
                }
            } catch (\Exception $e) {
                DB::rollBack();
                session()->flash('error', 'An error occurred: ' . $e->getMessage());
                return redirect()->back()->withInput();
            }
        }
        $header = 'CMO Data Fetching';
        return view('cmo.list', compact('header', 'inserted_id'));
    }

    public function populatelbportal(Request $request)
    {
        $id = $request->query('inserted_id');
        $record = CmoResponseJson::find($id);
        DB::beginTransaction();
        try {
            $records = json_decode($record->received_data, true);
            $collection = new Collection($records);
            $datas = $collection->map(function ($datas) {
                if (isset($datas['lgd_mun'])) {
                    $datas['lgd_muni'] = $datas['lgd_mun'];
                }
                unset($datas['doc_updated'], $datas['migration_id'], $datas['lgd_mun']);
                return $datas;
            });
            if (!empty($datas)) {
                $municipalities = Municipality::pluck('subdivision_id', 'lgd_code');
                $redressedStatusDefault = Codemaster::getIdByCode(3301);
                $redressedStatusFallback = Codemaster::getIdByCode(3306);
                foreach ($datas as $data) {
                    $cmoData = new CmoSmData();
                    $cmoData->fill($data);
                    $cmoData->lb_dist_code = $data['lgd_dist'];
                    if (isset($data['lgd_muni'])) {
                        $cmoData->lb_local_body_code = $municipalities[$data['lgd_muni']] ?? null;
                    } else {
                        $cmoData->lb_local_body_code = $data['lgd_block'];
                    }
                    $cmoData->lb_gp_ward_code = $data['ward_id'] ?? $data['gp_id'];
                    if (empty($data['lgd_muni']) && empty($data['lgd_block'])) {
                        $cmoData->redressed_status = $redressedStatusFallback;
                    } else {
                        $cmoData->redressed_status = $redressedStatusDefault;
                    }
                    $cmoData->save();
                }
                $record->is_fetched = 1;
                $record->save();
            }
            DB::commit();
            session()->flash('success', 'Data populated lbportal successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to populate lbportal: ' . $e->getMessage());
        }
        return redirect()->route('pullnewcmo');
    }

    public function cmogrievanceworkflow()
    {
        $header = 'Sarasori Mukhyamantri (CMO Grievance) List';
        $user = auth()->user();
        if (CheckAuthHelper::isCommonOperator()) {
            $workflow_dropdown_show = 0;
        } else {
            $workflow_dropdown_show = 1;
        }
        return view('cmo.cmogrievanceworkflow', compact('header', 'workflow_dropdown_show'));
    }

    public function cmogrievancefind(Request $request)
    {
        $grievance_id = Crypt::decryptString($request->id);
        $record = CmoSmData::find($grievance_id);
        $header = 'Find CMO Grievance Beneficiary';
        $atrs = CmoAtrMaster::all();
        $atr = CmoAtrMaster::find($record->atr_type);
        $applicant_details = '';
        if ($atr) {
            $data = BeneficiaryPersonalDetail::with('bank','contact')->find($record->lb_application_id);
            // dd($data->bank);
            $add = $data->contact->blockmuni();
            $bank = $data->bank->bankname();
            // dd($bank);
            $applicant_details = [
                'applicationId' => $data->application_id,
                'name' => $data->full_name,
                'dob' => $data->dob,
                'mobileNo' => $data->other_details['mobile_no'],
                'fatherName' => $data->ben_father_name,
                'blockMuni' => $add['block'],
                'gpWard' => $add['gp'],
                'bankName' => $data->bank['bank_name'],
                'branchName' => $data->bank['branch_name'],
                'ifscCode' => $data->bank['ifsc_code'],
                'accNo' => $data->bank['accno'],
            ];
        }
        $isaddvisible = 0;
        $user = auth()->user();
        if (CheckAuthHelper::isCommmonVerifier()) {
            $isaddvisible = 1;
            $isaddbutton = 0;
        } elseif (CheckAuthHelper::isCommonApprover()) {
            $isaddbutton = 1;
        } elseif (CheckAuthHelper::isCommonHOD()) {
            $isaddbutton = 2;
        }
        return view('cmo.cmo_details', compact('header', 'record', 'atrs', 'isaddvisible', 'isaddbutton', 'atr', 'applicant_details'));
    }

    public function cmodetailsaction(Request $request)
    {
        $rules = [
            'atr_type'     => 'required',
            'action_type'  => 'required|in:send_another_block,grievance_redressed',
            'id'           => 'required',
            'remarks'      => 'required|string|max:255',
        ];
        $messages = [
            'atr_type.*'     => 'ATR Type is required.',
            'remarks.*'           => 'Remarks is required and cannot exceed 255 characters.',
        ];
        if ($request->action_type === 'send_another_block') {
            $rules['district_id'] = 'required|integer';
            $rules['rural_urban'] = 'required|in:1,2';
            $rules['blockurban']  = 'required|integer';
            $messages['district_id.*'] = 'Please select a district.';
            $messages['rural_urban.*'] = 'Please select rural or urban.';
            $messages['blockurban.*']  = 'Please select block or municipality.';
        }
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        DB::beginTransaction();
        try {
            $atr_type = json_decode($request->atr_type, true);
            $action_type = $request->action_type;
            $grievance_id = Crypt::decryptString($request->id);
            $CmoSmData = CmoSmData::find($grievance_id);
            $old_data = $CmoSmData->toArray();
            $CmoSmData->atr_type = $atr_type['id'];
            $CmoSmData->remarks = $request->remarks;
            switch ($action_type) {
                case 'send_another_block':
                    $CmoSmData->lb_dist_code = $request->district_id;
                    if ($request->rural_urban == 1) {
                        $municipality = Municipality::where('lgd_code', $request->blockurban)->first();
                        $CmoSmData->lb_local_body_code = $municipality->subdivision_id;
                    } else {
                        $CmoSmData->lb_local_body_code = $request->blockurban;
                    }
                    $CmoSmData->old_data = $old_data;
                    $msg = 'The grievance has been sent to the corresponding block/subdivision successfully!';
                    break;
                case 'grievance_redressed':
                    $CmoSmData->is_redressed = 1;
                    $CmoSmData->redressed_status = Codemaster::getIdByCode(3302);
                    $CmoSmData->redressed_by = Auth::id();
                    $CmoSmData->redressed_date = now()->toDateString();
                    $msg = 'The grievance has been redressed successfully!';
                    break;
            }
            $CmoSmData->save();
            DB::commit();
            session()->flash('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Action failed: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
        return redirect()->route('cmo-grievance-workflow');
    }

    public function cmogrievancesearch(Request $request)
    {
        $validatedData = $request->validate([
            'action_type' => 'required|string|in:send_to_operator',
            'id' => 'required|string',
        ]);
        DB::beginTransaction();
        try {
            $action_type = $validatedData['action_type'];
            if ($action_type === 'send_to_operator') {
                $grievance_id = Crypt::decryptString($validatedData['id']);
                $CmoSmData = CmoSmData::find($grievance_id);
                $CmoSmData->send_to_op = 1;
                $CmoSmData->send_to_op_by = Auth::id();
                $CmoSmData->send_to_op_date = now()->toDateString();
                $CmoSmData->redressed_status = Codemaster::getIdByCode(3304);
                $CmoSmData->save();
                DB::commit();
                session()->flash('success', 'The grievance has been sent to the operator for new entry.');
                return redirect()->route('cmo-grievance-workflow');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Action failed: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function mapapplicant(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|string',
            'grievance_id' => 'required|string',
            'atr_type' => 'required|integer',
            'remarks' => 'string|max:255',
        ]);
        DB::beginTransaction();
        try {
            $id = Crypt::decryptString($validatedData['id']);
            $grievance_id = Crypt::decryptString($validatedData['grievance_id']);
            $CmoSmData = CmoSmData::find($grievance_id);
            $CmoSmData->lb_application_id = $id;
            $CmoSmData->redressed_status = Codemaster::getIdByCode(3302);
            $CmoSmData->atr_type = $validatedData['atr_type'];
            $CmoSmData->remarks = $validatedData['remarks'];
            $CmoSmData->is_mark = 1;
            $CmoSmData->save();
            DB::commit();
            session()->flash('success', 'The grievance has been mapped successfully.');
            return redirect()->route('cmo-grievance-workflow');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Action failed: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function addactions(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|string',
            'action_type' => 'required|in:pushtocmo,approve,revert',
        ]);
        DB::beginTransaction();
        try {
            $grievance_id = Crypt::decryptString($validatedData['id']);
            $action_type = $validatedData['action_type'];
            $msg = '';
            switch ($action_type) {
                case 'pushtocmo':
                    $CmoSmData = CmoSmData::where('grievance_id', $grievance_id)
                        ->where('is_processed', 2)
                        ->first();
                    $comment = $CmoSmData->remarks ?? '';
                    $comment = preg_replace('/\s+/', ' ', preg_replace('/[^a-zA-Z0-9 ]/', '', str_replace(["\t", "\n", "\r"], ' ', $comment)));
                    $comment = trim($comment);
                    $payload = [
                        "data" => [
                            [
                                "position_id" => 1,
                                "grievance_status" => "GM014",
                                "grievance_id" => null,
                                "comment" => $comment,
                                "bulk_grivance_id" => [$CmoSmData->grievance_id],
                                "assign_comment" => null,
                                "action_proposed" => null,
                                "urgency_flag" => null,
                                "addl_doc_id" => [],
                                "atn_id" => (int) $CmoSmData->atr_type,
                                "atn_reason_master_id" => null,
                                "action_taken_note" => $CmoSmData->atr_desc,
                                "contact_date" => null,
                                "tentative_date" => null,
                                "atr_doc_id" => [],
                                "action" => "TA"
                            ]
                        ]
                    ];
                    $response = $this->cmoAuthenticationService->submitNewATR($payload);
                    $cmoResponse = json_decode($response->getContent(), true);
                    if (
                        isset($cmoResponse['status'], $cmoResponse['message']) &&
                        $cmoResponse['status'] == 200 &&
                        $cmoResponse['message'] == 'Grievance status updated successfully'
                    ) {
                        $CmoSmData->redressed_status = Codemaster::getIdByCode(3305);
                        $CmoSmData->is_processed = 3;
                        $CmoSmData->response_back_by = Auth::id();
                        $CmoSmData->response_back_date = now();
                    } else {
                        throw new \Exception('Failed to update grievance status in CMO.');
                    }
                    $msg = 'The Grievance is pushed successfully.';
                    break;
                case 'approve':
                    $CmoSmData = CmoSmData::find($grievance_id);
                    $CmoSmData->is_processed = 2;
                    $CmoSmData->redressed_status = Codemaster::getIdByCode(3303);
                    $msg = 'The Grievance is approved successfully.';
                    break;
                case 'revert':
                    $CmoSmData = CmoSmData::find($grievance_id);
                    $CmoSmData->is_processed = 0;
                    $CmoSmData->redressed_status = Codemaster::getIdByCode(3301);
                    $msg = 'The Grievance is reverted successfully.';
                    break;
            }
            $CmoSmData->save();
            DB::commit();
            session()->flash('success', $msg);
            return redirect()->route('cmo-grievance-workflow');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Something went wrong: ' . $e->getMessage()]);
        }
    }
}
