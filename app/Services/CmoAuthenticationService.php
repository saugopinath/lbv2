<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Interfaces\CmoAuthenticationInterface;

class CmoAuthenticationService implements CmoAuthenticationInterface
{
    public function generateOTP()
    {
        try {
            $client = new Client();
            if (app()->environment(['local', 'staging'])) {
                $url = 'http://laravel.test/api/cmosvc/user/generateotp/';
            } else {
                $url = 'https://cmo.wb.gov.in/cmosvc/user/generateotp/';
            }
            $response = $client->post($url, [
                'json' => [
                    'user_name' => '9559000099'
                ]
            ]);
            $body = json_decode($response->getBody());
            if ($body->Exception == false && $body->Errors == null) {
                Cache::put('cmo_otp_status', 'sent', 600);
                return 1;
            } elseif (
                $body->Exception == true &&
                isset($body->Errors->Info[0]->Code) &&
                $body->Errors->Info[0]->Code == "IN043"
            ) {
                Cache::put('cmo_otp_status', 'already_sent', 600);
                return 1;
            } else {
                return 0;
            }
        } catch (\Exception $e) {
            // return -1;/
            return 'Error: ' . $e->getMessage();
        }
    }

    public function authiticated()
    {
        try {
            $client = new Client();
            if (app()->environment(['local', 'staging'])) {
                $url = 'http://laravel.test/api/cmosvc/user/login/';
            } else {
                $url = 'https://cmo.wb.gov.in/cmosvc/user/login/';
            }
            $response = $client->post($url, [
                'json' => [
                    'user_name' => '9559000099',
                    'otp' => '200825',
                    'login_as_position' => '14556'
                ]
            ]);
            $body = json_decode($response->getBody(), true);
            $httpCode = $response->getStatusCode();
            if ($httpCode == 200 && isset($body['Token'])) {
                return $body['Token'];
            }
            return null;
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    public function pullNewCmo($from_date, $to_date)
    {
        $token = '';
        $otp_return_status = $this->generateOTP();
        if ($otp_return_status == 1) {
            $token = $this->authiticated();
        } else {
            $token = $this->authiticated();
        }
        if ($token != '') {
            $tokenParts = explode('.', $token);
            $tokenPayload = base64_decode($tokenParts[1]);
            $payloadData = json_decode($tokenPayload);
        }
        if (isset($payloadData->exp)) {
            $tokenExpirationTime = $payloadData->exp;
            $currentTime = time();
            if ($tokenExpirationTime <= $currentTime) {
                $this->authiticated();
                $token = Cache::get('CMO_validate_token');
            }
        }
        if (app()->environment(['local', 'staging'])) {
            $base_url = 'http://laravel.test/api/cmosvc/shared/';
        } else {
            $base_url = 'https://cmo.wb.gov.in/cmosvc/shared/';
        }
        $client = new \GuzzleHttp\Client([
            'base_uri' => $base_url,
            'timeout'  => 30,
        ]);
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => $token,
        ];
        $data = [
            "from_date_time" => $from_date,
            "to_date_time"   => $to_date,
            "grievance_category" => [43, 44, 45],
            "status" => "3"
        ];
        try {
            $response = $client->post('wcdpullgriev/', [
                'headers' => $headers,
                'json' => $data
            ]);
            $statusCode = $response->getStatusCode();
            $body = json_decode($response->getBody()->getContents());
            if ($body->Exception == false && $body->Errors == null) {
                dd(json_encode($body->Data));
                DB::beginTransaction();
                $insert = DB::table('cmo.cmo_response_json')->insert([
                    'fetch_request_token' => $token,
                    'received_data' => json_encode($body->Data->details),
                    'from_date' => $from_date,
                    'to_date' => $to_date
                ]);
                if ($insert) {
                    DB::commit();
                    return response()->json(['status' => 200]);
                } else {
                    DB::rollback();
                    return response()->json(['status' => 400]);
                }
            } else {
                $message = isset($body->Errors->Business_Errors[0]->Message)
                    ? $body->Errors->Business_Errors[0]->Message
                    : 'Something went wrong! Please try again.';
                return response()->json([
                    'status' => 300,
                    'message' => $message,
                ]);
            }
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage()
            ]);
        }
    }
}
