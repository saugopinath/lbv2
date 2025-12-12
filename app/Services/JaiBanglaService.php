<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Interfaces\JaiBanglaInterface;

class JaiBanglaService implements JaiBanglaInterface
{
    protected $baseurl;
    public function __construct()
    {
        if (app()->environment(['local', 'staging'])) {
            $this->baseurl = 'http://laravel.test/api/';
        } else {
            $this->baseurl = 'https://jaibangla.wb.gov.in/';
        }
    }
    public function athentication()
    {
        try {
            $client = new Client();
            $url = $this->baseurl . 'jaibanglaapi/auth/login';
            $response = $client->post($url, [
                'json' => [
                    'email' => 'test@example.com',
                    'password' => 'password',
                ]
            ]);
            $body = json_decode($response->getBody());
            if ($body->is_success) {
                return $body->token;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }
    public function backfromjb()
    {
        if ($this->athentication()) {
            try {
                $client = new Client();
                $url = $this->baseurl . 'backfromjb';
                $response = $client->post($url, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->athentication(),
                        'Accept'        => 'application/json',
                    ]
                ]);
                $body = json_decode($response->getBody());
                dd($body->message);
            } catch (\Exception $e) {
                return 'Error: ' . $e->getMessage();
            }
        } else {
            dd('Token is invalid');
        }
    }
    public function logoutfromjb()
    {
        if ($this->athentication()) {
            try {
                $client = new Client();
                $url = $this->baseurl . 'logout';
                $response = $client->post($url, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->athentication(),
                        'Accept'        => 'application/json',
                    ]
                ]);
                $body = json_decode($response->getBody());
                dd($body->message);
            } catch (\Exception $e) {
                return 'Error: ' . $e->getMessage();
            }
        } else {
            dd('Token is invalid');
        }
    }

    public function refreshtokenforjb()
    {
        if ($this->athentication()) {
            try {
                $client = new Client();
                $url = $this->baseurl . 'refresh';
                $response = $client->post($url, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->athentication(),
                        'Accept'        => 'application/json',
                    ]
                ]);
                $body = json_decode($response->getBody());
                dd($body->token);
            } catch (\Exception $e) {
                return 'Error: ' . $e->getMessage();
            }
        } else {
            dd('Token is invalid');
        }
    }
}
