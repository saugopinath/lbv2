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
        return view('cmo.cmogrievanceworkflow', compact('header'));
    }

   public function cmogrievancefind($id)
   {
    dd(Crypt::decryptString($id));
   }
}
