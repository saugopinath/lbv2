<?php

namespace App\Services;

use App\Interfaces\JnmpAuthenticationInterface;
use App\Models\JnmpData;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JnmpAuthenticationService implements JnmpAuthenticationInterface
{
    protected $baseurl;
    private $username;
    private $password;
    public function __construct()
    {
        if (app()->environment(['local', 'staging'])) {
            $this->baseurl = 'http://laravel1.test/';
        } else {
            $this->baseurl = 'http://172.25.152.26:8084/';
        }

        set_time_limit(0);
        date_default_timezone_set('Asia/Kolkata');
        $this->username = 'WbjaybanglaDept';
        $this->password = '6voYkShku3qDLny0jORbWmtaPKyjZi94Ksl8lhL1M8N80nGIM3i';
    }


    public function getJnmpData($data)
    {
        set_time_limit(0);

        try {

            $from_date  = $data['from_date'];
            $to_date    = $data['to_date'];
            $index      = $data['index'];
            $page_size  = $data['page_size'];

            $auth_token = base64_encode($this->username . ':' . $this->password);


            $post_url = $this->baseurl . 'api/WbDeath'
                . '?FromDate=' . $from_date
                . '&ToDate=' . $to_date
                . '&PageIndex=' . $index
                . '&PageSize=' . $page_size;

            $client = new \GuzzleHttp\Client(['timeout' => 30]);

            $apiResponse = $client->get($post_url, [
                'headers' => [
                    'Authorization' => 'Basic ' . $auth_token,
                    'Accept'        => 'application/json'
                ]
            ]);

            $decoded = json_decode($apiResponse->getBody());
            if (!isset($decoded->data) || !is_array($decoded->data)) {
                return response()->json([
                    'status'  => 300,
                    'message' => "Invalid response from server."
                ]);
            }

            $data     = $decoded->data;
            $totalRec = $decoded->TotalRec ?? count($data);

            DB::beginTransaction();

            $bulkData = [];
            $currentTime = now();

            foreach ($data as $item) {

                $bulkData[] = [
                    'slno'                          => $item->slno,
                    'applicationid'                 => $item->applicationid,
                    'reportingdate'                 => $item->reportingdate,
                    'dateofdeath'                   => $item->dateofdeath,
                    'genderdesc'                    => $item->genderdesc,
                    'deceased_agetypedesc'          => $item->deceased_agetypedesc,
                    'deceased_age'                  => $item->deceased_age,
                    'deceased_firstname'            => $item->deceased_firstname,
                    'deceased_middlename'           => $item->deceased_middlename,
                    'deceased_lastname'             => $item->deceased_lastname,
                    'deceasedfullname'              => $item->deceasedfullname,
                    'deceased_idprooftyp'           => $item->deceased_idprooftyp,
                    'deceased_idprooftypname'       => $item->deceased_idprooftypname,
                    'deceasedkhadyosathicategoryid' => $item->deceasedkhadyosathicategoryid,
                    'deceasedkhadyosathicatdesc'    => $item->deceasedkhadyosathicatdesc,
                    'deceased_idproofnumber'        => $item->deceased_idproofnumber,
                    'present_districtname'          => $item->present_districtname,
                    'present_isblockorulbdesc'      => $item->present_isblockorulbdesc,
                    'present_blockmunicipalitydesc' => $item->present_blockmunicipalitydesc,
                    'present_pin'                   => $item->present_pin,
                    'present_grampanchayatdesc'     => $item->present_grampanchayatdesc,
                    'present_villagetowndesc'       => $item->present_villagetowndesc,
                    'certificateno'                 => $item->certificateno,

                    'fetching_time'                 => $currentTime,
                    'running_id'                    => $index,

                    'from_date'                     => date('Y-m-d', strtotime(str_replace('/', '-', $from_date))),
                    'to_date'                       => date('Y-m-d', strtotime(str_replace('/', '-', $to_date))),

                    'aadhar_hash'                   => (!empty($item->deceased_idprooftypname) &&
                        trim($item->deceased_idprooftypname) == 'Aadhaar')
                        ? md5($item->deceased_idproofnumber)
                        : null,
                ];
            }

            try {
                JnmpData::insert($bulkData);
            } catch (\Exception $e) {
                dd($e->getMessage());
            }


            $record_insert = count($bulkData);

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
    public function detailsCallBack($data)
    {
        set_time_limit(0);

        try {
            $limit = $data->limit ?? 10;

            $rows = JnmpData::where('is_details_callback', 0)
                ->limit($limit)
                ->get();

            if ($rows->isEmpty()) {
                return response()->json([
                    'status'  => 1,
                    'type'    => 'blue',
                    'icon'    => 'fa fa-info',
                    'title'   => 'Info',
                    'message' => 'No records found for callback.'
                ]);
            }

            $jsonArr = [];
            $applicationIds = [];

            foreach ($rows as $item) {
                $jsonArr[] = [
                    "ApplicationId" => $item->applicationid
                ];
                $applicationIds[] = $item->applicationid;
            }

            $auth_token = base64_encode($this->username . ':' . $this->password);

            $client = new \GuzzleHttp\Client(['timeout' => 30]);
            $apiUrl = $this->baseurl . 'api/WbDeathDetailsCallBack';

            // API CALL
            $response = $client->post($apiUrl, [
                'headers' => [
                    'Authorization' => 'Basic ' . $auth_token,
                    'Content-Type'  => 'application/json'
                ],
                'json' => $jsonArr
            ]);

            $decoded = json_decode($response->getBody());

            $ResponseDesc   = $decoded->ResponseDesc   ?? 'No message returned.';
            $StatusCode     = $decoded->HttpStatusCode ?? 500;
            $ResponseType   = $decoded->ResponseType   ?? 'Unknown';

            if ($StatusCode == 200) {

                JnmpData::whereIn('applicationid', $applicationIds)
                    ->update([
                        'details_callback_at' => now(),
                        'is_details_callback' => 1
                    ]);

                return response()->json([
                    'status'  => 1,
                    'type'    => 'green',
                    'icon'    => 'fa fa-check',
                    'title'   => 'Success',
                    'ResponseType' => $ResponseType,
                    'message' => $ResponseDesc
                ]);
            }

            return response()->json([
                'status'  => 400,
                'type'    => 'red',
                'icon'    => 'fa fa-times',
                'title'   => 'API Error',
                'message' => $ResponseDesc
            ]);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return response()->json([
                'status'  => 500,
                'type'    => 'red',
                'icon'    => 'fa fa-warning',
                'title'   => 'Request Exception',
                'message' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 500,
                'type'    => 'red',
                'icon'    => 'fa fa-warning',
                'title'   => 'System Error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
