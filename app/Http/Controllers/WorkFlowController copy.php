<?php

namespace App\Http\Controllers;

use App\Models\Codemaster;
use App\Models\OfficeMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class WorkFLowController extends Controller
{

    protected $district_id;
    protected $block_code;
    protected $subdivision_code;
    protected $office_type_id;
    public function __construct()
    {


    }

    protected function initializeFromSession()
    {

        $lgd_session = session('lgd_session');

        if (isset($lgd_session['office_type_id'])) {
            $this->office_type_id = Crypt::decryptString($lgd_session['office_type_id']);
        }

        if (isset($lgd_session['district_id'])) {
            $this->district_id = Crypt::decryptString($lgd_session['district_id']);
        }

        if (isset($lgd_session['block_id'])) {
            $this->block_code = Crypt::decryptString($lgd_session['block_id']);
        }

        if (isset($lgd_session['subdivision_id'])) {
            $this->subdivision_code = Crypt::decryptString($lgd_session['subdivision_id']);
        }


        // dump($lgd_session);
    }

    public function index()
    {
        $this->initializeFromSession();


        // dd($office_type_name);
        $data = [

            'district_id' => $this->district_id,
            'block_code' => $this->block_code,
            'subdivision_code' => $this->subdivision_code,
            'office_type_id' => $this->office_type_id,


        ];
        // dd($data);

        return view('WorkFLow.SubmittedList', compact('data'));


    }
}
