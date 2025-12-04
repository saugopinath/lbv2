<?php

namespace App\Services;

use App\Interfaces\JnmpAuthenticationInterface;
use App\Models\JnmpData;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class JnmpAuthenticationService implements JnmpAuthenticationInterface
{
    protected $baseurl;
    private $username;
    private $password;
    public function __construct()
    {
        if (app()->environment(['local', 'staging'])) {
            $this->baseurl = 'http://laravel.test/api/';
        } else {
            $this->baseurl = 'http://172.25.152.26:8084/';
        }

        set_time_limit(0);
        date_default_timezone_set('Asia/Kolkata');
        $this->username = 'WbjaybanglaDept';
        $this->password = '6voYkShku3qDLny0jORbWmtaPKyjZi94Ksl8lhL1M8N80nGIM3i';
    }


    public function getJnmpData($request)
    {
        set_time_limit(0);

        try {

            $from_date  = $request['from_date'];
            $to_date    = $request['to_date'];
            $index      = $request['index'];
            $page_size  = $request['page_size'];

            $auth_token = base64_encode($this->username . ':' . $this->password);

            $post_url = $this->baseurl . 'api/WbDeath?FromDate=' . $from_date .
                '&ToDate=' . $to_date .
                '&PageIndex=' . $index .
                '&PageSize=' . $page_size;

            $client = new \GuzzleHttp\Client(['timeout' => 30]);

            $apiResponse = $client->get($post_url, [
                'headers' => [
                    'Authorization' => 'Basic ' . $auth_token,
                    'Content-Type'  => 'application/json'
                ]
            ]);

            $decoded = json_decode($apiResponse->getBody());

            if (!isset($decoded->data) || !is_array($decoded->data)) {
                return response()->json([
                    'status'  => 300,
                    'message' => "Invalid response from server."
                ]);
            }

            $data      = $decoded->data;
            $totalRec  = $decoded->TotalRec ?? count($data);

            $record_insert = 0;

            DB::beginTransaction();

            foreach ($data as $item) {

                $JnmpDataModel = new JnmpData;

                $JnmpDataModel->slno                        = $item->slno;
                $JnmpDataModel->applicationid               = $item->ApplicationId;
                $JnmpDataModel->reportingdate               = $item->ReportingDate;
                $JnmpDataModel->dateofdeath                 = $item->DateOfDeath;
                $JnmpDataModel->genderdesc                  = $item->GenderDesc;
                $JnmpDataModel->deceased_agetypedesc        = $item->Deceased_AgeTypeDesc;
                $JnmpDataModel->deceased_age                = $item->Deceased_Age;
                $JnmpDataModel->deceased_firstname          = $item->Deceased_FirstName;
                $JnmpDataModel->deceased_middlename         = $item->Deceased_MiddleName;
                $JnmpDataModel->deceased_lastname           = $item->Deceased_LastName;
                $JnmpDataModel->deceasedfullname            = $item->DeceasedFullName;
                $JnmpDataModel->deceased_idprooftyp         = $item->Deceased_IdProofTyp;
                $JnmpDataModel->deceased_idprooftypname     = $item->Deceased_IdProofTypName;
                $JnmpDataModel->deceasedkhadyosathicategoryid = $item->DeceasedKhadyoSathiCategoryID;
                $JnmpDataModel->deceasedkhadyosathicatdesc  = $item->DeceasedKhadyoSathiCatDesc;
                $JnmpDataModel->deceased_idproofnumber      = $item->Deceased_IdProofNumber;
                $JnmpDataModel->present_districtname        = $item->Present_DistrictName;
                $JnmpDataModel->present_isblockorulbdesc    = $item->Present_IsBlockOrUlbDesc;
                $JnmpDataModel->present_blockmunicipalitydesc = $item->Present_BlockMunicipalityDesc;
                $JnmpDataModel->present_pin                 = $item->Present_Pin;
                $JnmpDataModel->present_grampanchayatdesc   = $item->Present_GramPanchayatDesc;
                $JnmpDataModel->present_villagetowndesc     = $item->Present_VillageTownDesc;
                $JnmpDataModel->certificateno               = $item->CertificateNo;

                $JnmpDataModel->fetching_time = now();
                $JnmpDataModel->running_id    = $index;
                $JnmpDataModel->from_date     = date('Y-m-d', strtotime(str_replace('/', '-', $from_date)));
                $JnmpDataModel->to_date       = date('Y-m-d', strtotime(str_replace('/', '-', $to_date)));

                $JnmpDataModel->aadhar_hash = (trim($item->Deceased_IdProofTypName) == 'Aadhaar')
                    ? md5($item->Deceased_IdProofNumber)
                    : null;

                if ($JnmpDataModel->save()) {
                    $record_insert++;
                }
            }

            DB::commit();

            return response()->json([
                'status'     => 200,
                'message'    => "Total {$record_insert} out of {$totalRec} imported successfully.",
                'inserted'   => $record_insert,
                'total_data' => $totalRec
            ]);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            DB::rollback();
            return response()->json([
                'status'  => 500,
                'message' => "API Request Error: " . $e->getMessage()
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status'  => 500,
                'message' => "System Error: " . $e->getMessage()
            ]);
        }
    }


    public function detailsCallBack($request)
    {
        set_time_limit(0);

        try {
            $limit = $request->limit;

            $data = JnmpData::where('is_details_callback', 0)->limit($limit)->get();

            if ($data->isEmpty()) {
                return response()->json([
                    'status' => 1,
                    'type' => 'blue',
                    'icon' => 'fa fa-info',
                    'title' => 'Info',
                    'message' => 'No records found.'
                ]);
            }

            $jsonArr = [];
            $updateIds = [];

            foreach ($data as $row) {
                $jsonArr[]  = ["ApplicationId" => $row->applicationid];
                $updateIds[] = $row->applicationid;
            }

            $auth_token = base64_encode($this->username . ':' . $this->password);


             $client = new \GuzzleHttp\Client(['timeout' => 30]);

            $apiUrl = $this->baseurl . 'api/WbDeathDetailsCallBack';

            $apiResponse = $client->post($apiUrl, [
                'headers' => [
                    'Authorization' => 'Basic ' . $auth_token,
                    'Content-Type' => 'application/json'
                ],
                'json' => $jsonArr
            ]);

            $decoded = json_decode($apiResponse->getBody());

            $ResponseDesc     = $decoded->ResponseDesc ?? '';
            $HttpStatusCode   = $decoded->HttpStatusCode ?? 500;
            $ResponseType     = $decoded->ResponseType ?? '';

            if ($HttpStatusCode == 200) {

                $updateValues = [
                    'details_callback_at' => now(),
                    'is_details_callback' => 1
                ];

                JnmpData::where('is_details_callback', 0)->whereIn('applicationid', $updateIds)->update($updateValues);

                return response()->json([
                    'ResponseType' => $ResponseType,
                    'status' => 1,
                    'type' => 'green',
                    'icon' => 'fa fa-check',
                    'title' => 'Success',
                    'message' => $ResponseDesc
                ]);
            }

            return response()->json([
                'status' => 400,
                'message' => 'API Error: ' . $ResponseDesc
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => $e->getMessage()
            ]);
        }
    }
}
