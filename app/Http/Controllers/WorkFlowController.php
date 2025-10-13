<?php

namespace App\Http\Controllers;

use App\Models\Codemaster;
use App\Models\OfficeMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class WorkFLowController extends Controller
{

    public function __construct() {}


    public function index()
    {
        $button_show = 1;
        return view('WorkFLow.SubmittedList',compact('button_show'));
    }
}
