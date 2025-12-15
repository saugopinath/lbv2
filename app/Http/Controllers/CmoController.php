<?php

namespace App\Http\Controllers;

use App\Helpers\CheckAuthHelper;
use App\Helpers\WorkFlowPermissionHelper;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Helpers\LgdFilterHelper;
use App\Models\BeneficiaryPersonal;
use App\Models\BenRejectDetails;
use App\Models\District;
use App\Models\DraftBeneficiaryPersonal;
use Database\Seeders\BenRejectDetailsSeeder;
use Illuminate\Support\Facades\Log;
use App\Exports\ArrayExport;
use Maatwebsite\Excel\Facades\Excel;

class CmoController extends Controller
{
    protected $cmoAuthenticationService;
    protected $isAuthorized = false;

    public function __construct(CmoAuthenticationInterface $cmoAuthenticationService)
    {
        $this->cmoAuthenticationService = $cmoAuthenticationService;
        // dd('ok');
        // if (CheckAuthHelper::isCommonCMOController()) {
        //     // dd('ok1');
        //     $this->isAuthorized = true;
        // } else {
        //     redirect()->route('dashboard')
        //         ->with('error', 'Oops! You are not authorized to perform this action.')
        //         ->send();
        // }
    }

    public function pullnewcmo(Request $request)
    {
        if (CheckAuthHelper::isSuperAdmin()) {
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
        $header = 'Oops! You do not have permission.';
        return view('CommonRestictedpage.index', compact('header'));
    }

    public function populatelbportal(Request $request)
    {
        if (CheckAuthHelper::isSuperAdmin()) {
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
                    foreach ($datas as $data) {
                        $cmoData = new CmoSmData();
                        $cmoData->fill($data);
                        $cmoData->lb_dist_code = $data['lgd_dist'];
                        if (isset($data['lgd_muni'])) {
                            $cmoData->lb_local_body_code = Municipality::where('lgd_code', $data['lgd_muni'])->first()->subdivision_id;
                        } else {
                            $cmoData->lb_local_body_code = $data['lgd_block'];
                        }
                        $cmoData->lb_gp_ward_code = $data['ward_id'] ?? $data['gp_id'];
                        $cmoData->redressed_status = Codemaster::getIdByCode(3301);
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
        $header = 'Oops! You do not have permission.';
        return view('CommonRestictedpage.index', compact('header'));
    }

    public function cmogrievanceworkflow()
    {
        $header = 'Sarasori Mukhyamantri (CMO Grievance) List';

        if (CheckAuthHelper::isCommonOperator()) {
            $workflow_dropdown_show = 0;
        } else {
            $workflow_dropdown_show = 1;
        }
        return view('cmo.cmogrievanceworkflow', compact('header', 'workflow_dropdown_show'));
    }

    public function cmogrievancefind(Request $request)
    {
        if (CheckAuthHelper::isCommonFindUser()) {
            $grievance_id = Crypt::decryptString($request->id);
            $record = CmoSmData::find($grievance_id);
            $header = 'Find CMO Grievance Beneficiary';
            $atrs = CmoAtrMaster::all();
            $atr = CmoAtrMaster::find($record->atr_type);
            $applicant_details = '';
            if ($atr) {
                $data = BeneficiaryCommonList::with('sourceable.relationships', 'sourceable.contact', 'sourceable.bank')->find($record->lb_application_id);
                $add = $data->sourceable->contact->blockmuni();
                $bank = $data->sourceable->bank->bankname();
                $applicant_details = [
                    'applicationId' => $data->sourceable->application_id,
                    'name' => $data->sourceable->full_name,
                    'dob' => $data->sourceable->dob,
                    'mobileNo' => $data->sourceable->mobile_no,
                    'fatherName' => $data->sourceable->relationships->first()->getFullNameByCode(131),
                    'blockMuni' => $add['block'],
                    'gpWard' => $add['gp'],
                    'bankName' => $bank['bank_name'],
                    'branchName' => $bank['branch_name'],
                    'ifscCode' => $bank['ifsc_code'],
                    'accNo' => $bank['accno'],
                ];
            }
            $isaddvisible = 0;

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
        $header = 'Oops! You do not have permission.';
        return view('CommonRestictedpage.index', compact('header'));
    }

    public function cmodetailsaction(Request $request)
    {
        if (CheckAuthHelper::isCommmonVerifier()) {
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
        $header = 'Oops! You do not have permission.';
        return view('CommonRestictedpage.index', compact('header'));
    }

    public function cmogrievancesearch(Request $request)
    {
        if (CheckAuthHelper::isCommmonVerifier()) {
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
        $header = 'Oops! You do not have permission.';
        return view('CommonRestictedpage.index', compact('header'));
    }

    public function mapapplicant(Request $request)
    {
        if (CheckAuthHelper::isCommmonVerifier()) {
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
        $header = 'Oops! You do not have permission.';
        return view('CommonRestictedpage.index', compact('header'));
    }

    public function addactions(Request $request)
    {
        if (CheckAuthHelper::isCommonAppHod()) {
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
        $header = 'Oops! You do not have permission.';
        return view('CommonRestictedpage.index', compact('header'));
    }

    public function cmoMisReport(Request $request)
    {
        $district_dropdown = false;
        if (CheckAuthHelper::isCommonHOD()) {
            $district_dropdown = true;
        }
        $massage = 'CMO Mis Report';
        $helperData = LgdFilterHelper::getCodesAndInitialCounts($request);
        $masterLocations = $helperData['master_locations'] ?? [];
        $mode = $helperData['mode'] ?? null;
        $col = $helperData['col'] ?? null;
        $name = $helperData['name'] ?? null;
        $blockIds = $helperData['block_ids'] ?? [];
        $subdivisionIds = $helperData['sub_division_ids'] ?? [];
        $actionPending = [Codemaster::getIdByCode(3301)];
        $approvalPending = [Codemaster::getIdByCode(3302), Codemaster::getIdByCode(3304)];
        $pushtoCMOPending = [Codemaster::getIdByCode(3303)];
        $totalPushed = [Codemaster::getIdByCode(3305)];
        $baseFilters = [];
        if (!empty($helperData['district_code'])) {
            $baseFilters['lb_dist_code'] = $helperData['district_code'];
        }
        if (!empty($helperData['block_code'])) {
            $baseFilters['lb_local_body_code'] = $helperData['block_code'];
        }
        if (!empty($helperData['subdivission_code'])) {
            $baseFilters['lb_local_body_code'] = $helperData['subdivission_code'];
        }
        if (!empty($helperData['rural_urban_code'])) {
            $baseFilters['address_type'] = $helperData['rural_urban_code'];
        }
        if (!empty($helperData['gpWard_code'])) {
            $baseFilters['cd_gp_ward_id'] = $helperData['gpWard_code'];
        }
        $locationCounts = [];
        $locationNames = [];
        $columns = $this->getColumnsByMode($mode);
        foreach ($masterLocations as $loc) {
            $key = $loc['location_id'];
            $locationNames[$key] = $loc['location_name'];
            $locationCounts[$key] = [
                'location_name' => $loc['location_name'],
                'actionPending' => 0,
                'approvalPending' => 0,
                'pushtoCMOPending' => 0,
                'totalPushed' => 0,
            ];
        }
        if (empty($masterLocations)) {

            return view('cmo.cmo_count_report', [
                'header'  => $massage,
                'helper'  => $helperData,
                'columns' => $columns,
                'name' => $name,
                'data'    => []
            ]);
        }
        $baseQuery = $this->buildBaseQuery($baseFilters);
        if ($mode === 'block_subdivision') {
            // dd('ok');
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
                return view('cmo.cmo_count_report', [
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
                        'actionPending' => 0,
                        'approvalPending' => 0,
                        'pushtoCMOPending' => 0,
                        'totalPushed' => 0,
                    ];
                }
                $query = (clone $baseQuery)->where('lb_local_body_code', $blockId);
                $total = $query->count();
                $locationCounts[$key]['actionPending'] = $this->countByRoleId((clone $query), $actionPending);
                $locationCounts[$key]['approvalPending'] = $this->countByRoleId((clone $query), $approvalPending);
                $locationCounts[$key]['pushtoCMOPending'] = $this->countByRoleId((clone $query), $pushtoCMOPending);
                $locationCounts[$key]['totalPushed'] = $this->countByRoleIdwithflag((clone $query), $totalPushed);
            }
            foreach ($subdivisionIds as $subId) {
                $key = 'sub_' . $subId;
                if (!isset($locationCounts[$key])) {
                    $locationCounts[$key] = [
                        'location_name' => $locationNames[$key] ?? "Subdivision {$subId}",
                        'actionPending' => 0,
                        'approvalPending' => 0,
                        'pushtoCMOPending' => 0,
                        'totalPushed' => 0,
                    ];
                }
                $query = (clone $baseQuery)->where('lb_local_body_code', $subId);
                $total = $query->count();
                $locationCounts[$key]['actionPending'] = $this->countByRoleId((clone $query), $actionPending);
                $locationCounts[$key]['approvalPending'] = $this->countByRoleId((clone $query), $approvalPending);
                $locationCounts[$key]['pushtoCMOPending'] = $this->countByRoleId((clone $query), $pushtoCMOPending);
                $locationCounts[$key]['totalPushed'] = $this->countByRoleIdwithflag((clone $query), $totalPushed);
            }
            $unmappedQuery = (clone $baseQuery)
                ->whereNull('lb_local_body_code');
            $actionPendingUnmapped = $this->countByRoleId(
                (clone $unmappedQuery),
                $actionPending
            );
            $approvalPendingUnmapped = $this->countByRoleId(
                (clone $unmappedQuery),
                $approvalPending
            );
            $pushtoCMOPendingUnmapped = $this->countByRoleId(
                (clone $unmappedQuery),
                $pushtoCMOPending
            );
            $totalPushedUnmapped = $this->countByRoleIdwithflag(
                (clone $unmappedQuery),
                $totalPushed
            );
            $unmappedTotal =
                $actionPendingUnmapped +
                $approvalPendingUnmapped +
                $pushtoCMOPendingUnmapped +
                $totalPushedUnmapped;
            if ($unmappedTotal > 0) {
                $locationCounts['unmapped'] = [
                    'location_name'      => 'Unmapped (Block & Sub-Div null)',
                    'actionPending'      => $actionPendingUnmapped,
                    'approvalPending'    => $approvalPendingUnmapped,
                    'pushtoCMOPending'   => $pushtoCMOPendingUnmapped,
                    'totalPushed'        => $totalPushedUnmapped,
                ];
            }
        } else {
            if ($col === 'block_id' || $col === 'subdivision_id') {
                $col = 'lb_local_body_code';
            }
            if ($col === 'district_id') {
                $col = 'lb_dist_code';
            }
            if (empty($col)) {
                $col = 'lb_dist_code';
            }
            // dd('ok1');
            $ids = [];
            foreach ($masterLocations as $loc) {
                if (is_numeric($loc['location_id'])) {
                    $ids[] = (int)$loc['location_id'];
                }
            }
            if (empty($ids)) {

                return view('cmo.cmo_count_report', [
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
                        'actionPending' => 0,
                        'approvalPending' => 0,
                        'pushtoCMOPending' => 0,
                        'totalPushed' => 0,
                    ];
                }
                $query = (clone $baseQuery)->where($col, $locId);
                $total = $query->count();
                $locationCounts[$locKey]['actionPending'] = $this->countByRoleId((clone $query), $actionPending);
                $locationCounts[$locKey]['approvalPending'] = $this->countByRoleId((clone $query), $approvalPending);
                $locationCounts[$locKey]['pushtoCMOPending'] = $this->countByRoleId((clone $query), $pushtoCMOPending);
                $locationCounts[$locKey]['totalPushed'] = $this->countByRoleIdwithflag((clone $query), $totalPushed);
            }
        }
        foreach ($locationCounts as &$counts) {
            $counts['actionPending'] = (int)($counts['actionPending'] ?? 0);
            $counts['approvalPending'] = (int)($counts['approvalPending'] ?? 0);
            $counts['pushtoCMOPending'] = (int)($counts['pushtoCMOPending'] ?? 0);
            $counts['totalPushed'] = (int)($counts['totalPushed'] ?? 0);
        }
        $data = [];
        foreach ($locationCounts as $key => $row) {
            $actionPending  = (int)($row['actionPending'] ?? 0);
            $approvalPending = (int)($row['approvalPending'] ?? 0);
            $pushtoCMOPending = (int)($row['pushtoCMOPending'] ?? 0);
            $totalPushed = (int)($row['totalPushed'] ?? 0);
            $total = $actionPending + $approvalPending + $pushtoCMOPending + $totalPushed;
            $data[] = [
                'location_name' => $row['location_name'] ?? $key,
                'actionPending' => $actionPending,
                'approvalPending' => $approvalPending,
                'pushtoCMOPending' => $pushtoCMOPending,
                'totalPushed' => $totalPushed,
                'total' => $total,
            ];
        }
        return view('cmo.cmo_count_report', [
            'header' => $massage,
            'helper' => $helperData,
            'columns' => $columns,
            'data' => $data,
            'name' => $name,
            'ruralUrban' => $request->rural_urban,
            'district_dropdown' => $district_dropdown,
            'districts' => District::all(),
            'selectedDistrict' => $request->district_id,
            // 'exportUrl' => route('reports-export'),
            // 'filename' => 'application-mis-report.xlsx',
        ]);
    }

    private function getColumnsByMode(?string $mode,): array
    {
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
            ['key' => 'total', 'label' => 'Total Grievance', 'align' => 'right', 'type' => 'number', 'show_total' => true],
            ['key' => 'actionPending', 'label' => 'Total Action Pending', 'align' => 'right', 'type' => 'number', 'show_total' => true],
            ['key' => 'approvalPending', 'label' => 'Total Approval Pending Among Action taken', 'align' => 'right', 'type' => 'number', 'show_total' => true],
            ['key' => 'pushtoCMOPending', 'label' => 'Total Approved but Pushed To CMO pending', 'align' => 'right', 'type' => 'number', 'show_total' => true],
            ['key' => 'pushtoCMOPending', 'label' => 'Total Pushed To CMO', 'align' => 'right', 'type' => 'number', 'show_total' => true],
        ];
    }
    private function buildBaseQuery(array $baseFilters)
    {
        $query = CmoSmData::query();
        foreach ($baseFilters as $column => $value) {
            $query->where($column, $value);
        }
        return $query;
    }
    private function countByRoleId($query, array $roleIds): int
    {
        $count = (clone $query)
            ->whereIn('redressed_status', $roleIds)
            ->count();
        return $count;
    }
    private function countByRoleIdwithflag($query, array $roleId): int
    {
        $count = (clone $query)
            ->whereIn('redressed_status', $roleId)
            ->count();
        return $count;
    }
}
