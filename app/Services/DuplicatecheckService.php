<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Interfaces\DuplicatecheckInterface;

class DuplicatecheckService implements DuplicatecheckInterface
{
    protected $baseurl;
    public function __construct()
    {
        if (app()->environment(['local', 'staging'])) {
            $this->baseurl = 'http://laravel.test/api/';
        } else {
            $this->baseurl = '';
        }
    }

    public function authentication()
    {
        try {
            $client = new Client();
            $url = $this->baseurl . 'auth/login';
            $response = $client->post($url, [
                'json' => [
                    'email' => 'approverdpaschimedinipur@gmail.com',
                    'password' => '1234',
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

    public function duplicatecheck($checkWith, $schemeId, $inputValue, $otherSchemes)
    {
        // dd($checkWith, $schemeId, $inputValue);
        // if ($this->authentication()) {
            try {
                $client = new Client();
                $url = $this->baseurl . 'duplicatecheck';
                $data = [
                    "checkWith" => $checkWith,
                    "schemeId" => $schemeId,
                    "inputValue" => $inputValue,
                    "otherSchemes" => $otherSchemes
                ];
                // dd($url,$data);
                // $response = $client->post($url, [
                //     'headers' => [
                //         'Authorization' => 'Bearer ' . $this->authentication(),
                //         'Accept'        => 'application/json',
                //     ],
                //     'form_params' => $data,
                // ]);
                $response = $client->post($url, [
                    'headers' => [
                        'Accept' => 'application/json',
                    ],
                    'form_params' => $data,
                ]);
                $body = json_decode($response->getBody());
                return $body;
            } catch (\Exception $e) {
            }
        // } else {
        //     dd('Token is invalid');
        // }
    }
    public function logout()
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

    public function refreshtoken()
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
