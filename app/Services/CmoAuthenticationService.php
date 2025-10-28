<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
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
}
