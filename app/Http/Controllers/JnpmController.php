<?php

namespace App\Http\Controllers;

use App\Interfaces\JNMPAuthenticationInterface;
use App\Models\BeneficiaryPersonalDetail;
use App\Models\Codemaster;
use App\Models\JnmpData;
use App\Models\LbMapping;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Helpers\CheckAuthHelper;
use App\Helpers\WorkFlowPermissionHelper;
use App\Models\District;

class JnpmController extends Controller
{
    protected $JnmpAuthenticationService;
    protected $isAuthorized = false;
    public function __construct(JnmpAuthenticationInterface $JnmpAuthenticationService)
    {
        if (CheckAuthHelper::isCommonJNMP()) {
            $this->isAuthorized = true;
        } else {
            redirect()->route('dashboard')
                ->with('error', 'Oops! You are not authorized to perform this action.')
                ->send();
        }
        $this->JnmpAuthenticationService = $JnmpAuthenticationService;
    }
    public function pullJnmpData(Request $request)
    {
        if (WorkFlowPermissionHelper::canImportJanmaMrityuData()) {
            $inserted = null;

            if ($request->isMethod('post')) {

                $from = \Carbon\Carbon::createFromFormat('d/m/Y', $request->from_date);
                $to   = \Carbon\Carbon::createFromFormat('d/m/Y', $request->to_date);

                $validateData = [
                    'from_date' => $from->format('Y-m-d'),
                    'to_date'   => $to->format('Y-m-d'),
                ];

                $rules = [
                    'from_date' => 'required|date',
                    'to_date'   => 'required|date|after_or_equal:from_date',
                ];

                $messages = [
                    'from_date.required' => 'Please select a valid start date.',
                    'to_date.required'   => 'Please select a valid end date.',
                ];

                $validator = Validator::make($validateData, $rules, $messages);

                if ($validator->fails()) {
                    return back()
                        ->withErrors($validator)
                        ->withInput();
                }

                DB::beginTransaction();

                try {

                    $index = 1;
                    $page_size = 1000;

                    $payload = [
                        'from_date' => $request->from_date,
                        'to_date'   => $request->to_date,
                        'index'     => $index,
                        'page_size' => $request->page_size ?? $page_size
                    ];

                    $response = $this->JnmpAuthenticationService->getJnmpData($payload);

                    $data = $response->getData(true);


                    if (($data['status'] ?? 500) == 200) {

                        DB::commit();

                        session()->flash('success', $data['message']);
                        session()->forget(['index', 'page_size']);

                        return redirect()->route('jnmp.pull', ['inserted' => $data['inserted']]);
                    }

                    DB::rollBack();
                    session()->flash('error', 'Failed to import JNMP data.');
                    return redirect()->back();
                } catch (\Exception $e) {

                    DB::rollBack();
                    session()->flash('error', 'Error: ' . $e->getMessage());
                    return redirect()->back()->withInput();
                }
            }
            //just add lastfetching date
            $lastFetchRaw = JnmpData::max('fetching_time');

            if ($lastFetchRaw) {
                $maxDate = Carbon::parse($lastFetchRaw);

                if ($maxDate->isToday()) {
                    $prevDate = JnmpData::whereDate('fetching_time', '<', $maxDate->toDateString())
                        ->orderBy('fetching_time', 'desc')
                        ->value('fetching_time');
                } else {
                    $prevDate = $lastFetchRaw;
                }

                $lastFetch = $prevDate
                    ? Carbon::parse($prevDate)->format('d/m/Y')
                    : 'N/A';
            } else {
                $lastFetch = 'N/A';
            }

            return view('jnmp.list', [
                'header' => 'Importing data from Janma-Mrityu Thathya Portal',
                'lastFetch' => $lastFetch,
            ]);
        }
        $header = 'Oops! You do not have permission to view users.';
        return view('CommonRestictedpage.index', compact('header'));
    }
    public function detailsCallback(Request $request)
    {
        try {

            $response = $this->JnmpAuthenticationService->detailsCallBack($request);

            $data = $response->getData(true);

            // If callback successful
            if (($data['status'] ?? 0) == 1) {
                session()->flash('success', $data['message']);
            } else {
                session()->flash('error', $data['message']);
            }

            return redirect()->back();
        } catch (\Exception $e) {

            session()->flash('error', 'System Error: ' . $e->getMessage());
            return redirect()->back();
        }
    }
    public function getJnmpStats()
    {
        $nextleveljnmp = Codemaster::getIdByCode(2300);
        $nextlevelapprove = Codemaster::getIdByCode(0);
        // JNMP DATA COUNTS
        $totalJnmp = JnmpData::count() ?? 0;

        $remainingJnmp = JnmpData::where('is_details_callback', 0)->count() ?? 0;
        $updatedJnmp = JnmpData::where('is_details_callback', 1)->count() ?? 0;

        // PERSONAL DETAILS COUNTS
        $jnmp_mark = BeneficiaryPersonalDetail::where('jnmp_marked', 1)->count() ?? 0;

        $cur_jnmp_mark_as_death = BeneficiaryPersonalDetail::where('next_level_role_id', $nextleveljnmp)->count() ?? 0;

        $re_activate = BeneficiaryPersonalDetail::where('jnmp_marked', 1)
            ->where('next_level_role_id', $nextlevelapprove)
            ->count() ?? 0;

        return response()->json([
            'status' => 1,
            'totalJnmp' => $totalJnmp,
            'remainingJnmp' => $remainingJnmp,
            'updatedJnmp' => $updatedJnmp,

            'data1' => $jnmp_mark,
            'data2' => $cur_jnmp_mark_as_death,
            'data3' => $re_activate,
        ]);
    }
    public function markAsDeathProcess()
    {
        DB::beginTransaction();

        try {
            $nextleveljnmp = Codemaster::getIdByCode(2300);
            $nextlevelapprove = Codemaster::getIdByCode(0);

            // INITIALIZE ARRAY ONLY ONCE
            $mappingData = [];
            $markNormal = 0;

            // STEP 1: JNMP Aadhaar Matched
            $jnmpAadhaarMatched = JnmpData::whereNull('lb_application_id')
                ->where('deceased_idprooftypname', 'Aadhaar')
                ->whereHas('aadhaar')
                ->get();

            foreach ($jnmpAadhaarMatched as $item) {

                // Update JNMP Data
                $item->lb_application_id = $item->aadhaar->application_id;
                $item->marking_application_at = now();
                $item->save();

                // ADD MAPPING DATA (CORRECT)
                $mappingData[] = [
                    'lb_id'           => $item->aadhaar->application_id,
                    'jnm_id'          => $item->applicationid,
                    'aadhar_hash'     => $item->aadhar_hash,
                    'payment_suspend' => 1,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ];
            }

            // STEP 2: Update BeneficiaryPersonalDetail
            $beneficiaries = BeneficiaryPersonalDetail::where('next_level_role_id', $nextlevelapprove)
                ->whereNull('jnmp_marked')
                ->whereHas('jnmp', function ($q) {
                    $q->where('migrated_to_jb', 0);
                })
                ->with('jnmp')
                ->get();

            foreach ($beneficiaries as $ben) {

                if ($ben->jnmp) {

                    $ben->update([
                        'next_level_role_id' => $nextleveljnmp,
                        'jnmp_marked'        => 1
                    ]);

                    $markNormal++;
                }
            }

            // STEP 3: INSERT MAPPING DATA
            if (!empty($mappingData)) {
                LbMapping::insert($mappingData);
            }

            DB::commit();

            session()->flash('success', "Marking Completed Successfully. Total Marked: $markNormal");
            return redirect()->back();
        } catch (\Exception $e) {

            DB::rollBack();
            session()->flash('error', 'Unexpected Error: ' . $e->getMessage());
            return redirect()->back();
        }
    }
    public function index()
    {
        if (WorkFlowPermissionHelper::canReActivateDeathIncident()) {
            $button_show = 1;
            return view('jnmp.index', compact('button_show'));
        }
        $header = 'Oops! You do not have permission to view users.';
        return view('CommonRestictedpage.index', compact('header'));
    }
    public function jnmpMarkedDataAtHOD(Request $request)
    {
        if (WorkFlowPermissionHelper::canJanmyaMrityuBeneficiaryList()) {
            if (CheckAuthHelper::isCommonHOD()) {

                $districts = District::all();

                $district = $request->district ?? null;

                return view('jnmp.linelisting_at_hod', compact('districts', 'district'));
            } else {
                redirect()->route('dashboard')
                    ->with('error', 'Oops! You are not authorized to perform this action.')
                    ->send();
            }
        }
        $header = 'Oops! You do not have permission to view users.';
        return view('CommonRestictedpage.index', compact('header'));
    }
}
