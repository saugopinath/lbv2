<?php

namespace App\Http\Controllers;

use App\Models\DraftBeneficiaryPersonal;
use Illuminate\Http\Request;

class LBController extends Controller
{
    public function __construct() {
    }

    public function index()
    {
        return view('lbform.lbform');
    }
    public function draftlist()
    {
        $list = DraftBeneficiaryPersonal::all();
        return view('lbform.draftlist');
    }
}
