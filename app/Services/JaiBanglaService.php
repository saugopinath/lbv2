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
                    'email' => 'test@gmail.com',
                    'password' => 'test',
                ]
            ]);
            $body = json_decode($response->getBody());
            dd($body);
            $token = '';
            if ($body->is_success == true) {
                $token = $body->token;
                return $token;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }
}
