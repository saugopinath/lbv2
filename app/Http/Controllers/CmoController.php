<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Interfaces\CmoAuthenticationInterface;

class CmoController extends Controller
{
    protected $cmoAuthenticationService;

    public function __construct(CmoAuthenticationInterface $cmoAuthenticationService)
    {
        $this->cmoAuthenticationService = $cmoAuthenticationService;
    }

    public function checkJson()
    {
        $client = new Client();
        try {
            $url = url('example.json');
            $response = $client->get($url);
            if ($response->getStatusCode() === 200) {
                $data = json_decode($response->getBody(), true);
                // $collection = collect($data);
                // dd($collection);
                return response()->json([
                    'status' => 'success',
                    'message' => 'File found and loaded successfully',
                    'data' => $data,
                ]);
            } else {
                return response()->json([
                    'status' => 'warning',
                    'message' => 'File found but returned status: ' . $response->getStatusCode(),
                ]);
            }
        } catch (RequestException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage(),
            ]);
        }
    }

    public function pullnewcmo(Request $request)
    {
        if ($request->isMethod('post')) {

            $from_date = $request->from_date;
            $to_date = $request->to_date;
            $data = $this->cmoAuthenticationService->pullNewCmo($from_date, $to_date);
        }
        $header = 'CMO Data Fetching';
        return view('cmo.list', compact('header'));
    }
}
