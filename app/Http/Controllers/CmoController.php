<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
class CmoController extends Controller
{
   public function checkJson()
    {
        $client = new Client();
        try {
            $url = url('example.json');
            $response = $client->get($url);
            if ($response->getStatusCode() === 200) {
                $data = json_decode($response->getBody(), true);
                return response()->json([
                    'status' => 'success',
                    'message' => 'File found and loaded successfully',
                    'data' => $data,
                ]);
            } else {
                return response()->json([
                    'status' => 'warning',
                    'message' => 'File found but returned status: '.$response->getStatusCode(),
                ]);
            }
        } catch (RequestException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error: '.$e->getMessage(),
            ]);
        }
    }
}
