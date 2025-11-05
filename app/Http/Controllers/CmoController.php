<?php

namespace App\Http\Controllers;

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
            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $data = $this->cmoAuthenticationService->pullNewCmo($from_date, $to_date);
            $response = json_decode($data->getContent(), true);
            if (isset($response['inserted_id']) && $response['status'] == 200) {
                $inserted_id = $response['inserted_id'];
                session()->flash('success', 'Data pulled successfully!');
                return redirect()->route('pullnewcmo', ['inserted_id' => $inserted_id]);
            } else {
                session()->flash('error', 'Failed to pull data.');
            }
        }
        $header = 'CMO Data Fetching';
        return view('cmo.list', compact('header', 'inserted_id'));
    }

    public function populatelbportal(Request $request)
    {
        $id = $request->query('inserted_id');
        $record = CmoResponseJson::find($id);
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
                // dd($data['lgd_muni']);
                $cmoData->lb_dist_code = $data['lgd_dist'];
                // $cmoData->lb_local_body_code = $data['lgd_block'] ?? $data['lgd_muni'];
                // dd($data['lgd_muni']);
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
    }

    public function cmogrievanceworkflow()
    {
        $header = 'Sarasori Mukhyamantri (CMO Grievance) List';
        $user = auth()->user();
        if ($user->hasRole('Operator')) {
            $workflow_dropdown_show = 0;
        } else{
            $workflow_dropdown_show = 1;
        }
        return view('cmo.cmogrievanceworkflow', compact('header','workflow_dropdown_show'));
    }

    //    public function cmogrievancefind($id)
    //    {
    //     dd(Crypt::decryptString($id));
    //    }

    public function cmogrievancefind(Request $request)
    {
        $grievance_id = Crypt::decryptString($request->id);
        $record = CmoSmData::find($grievance_id);
        $header = 'Find CMO Grievance Beneficiary';
        $atrs = CmoAtrMaster::all();
        // dd($atrs);
        return view('cmo.cmo_details', compact('header', 'record', 'atrs'));
    }

    public function cmodetailsaction(Request $request)
    {
        $atr_type = json_decode($request->atr_type, true);
        $action_type = $request->action_type;
        $grievance_id = Crypt::decryptString($request->id);
        $CmoSmData = CmoSmData::find($grievance_id);
        $old_data = $CmoSmData->toArray();
        $CmoSmData->atr_type = $atr_type['id'];
        $CmoSmData->remarks = $request->remarks;
        switch ($action_type) {
            case 'map_applicant':
                echo "map_applicant";
                break;
            case 'send_another_block':
                $CmoSmData->lb_dist_code = $request->district_id;
                if ($request->rural_urban == 1) {
                    $CmoSmData->lb_local_body_code = Municipality::where('lgd_code', $request->blockurban)->first()->subdivision_id;
                } else {
                    $CmoSmData->lb_local_body_code = $request->blockurban;
                }
                $CmoSmData->old_data = $old_data;
                break;
            case 'grievance_redressed':
                $CmoSmData->is_redressed = 1;
                $CmoSmData->redressed_status = Codemaster::getIdByCode(3302);
                $CmoSmData->redressed_by = Auth::id();
                $CmoSmData->redressed_date = now()->toDateString();
                break;
        }
        // dd($CmoSmData);
        $CmoSmData->save();
    }

    public function cmogrievancesearch(Request $request)
    {
        // dd($request->all());
        $action_type = $request->action_type;
        if ($action_type == 'send_to_operator') {
            $grievance_id = Crypt::decryptString($request->id);
            $CmoSmData = CmoSmData::find($grievance_id);
            $CmoSmData->send_to_op = 1;
            $CmoSmData->send_to_op_by = Auth::id();
            $CmoSmData->send_to_op_date = now()->toDateString();
            $CmoSmData->redressed_status = Codemaster::getIdByCode(3304);
            $CmoSmData->save();
            session()->flash('success', 'The Grievance Is Sent To Operator For New Entry');
            return redirect()->route('cmo-grievance-workflow');
        } elseif ($action_type == 'search') {
        }
    }
}
