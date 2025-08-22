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
        $lists = DraftBeneficiaryPersonal::paginate(10);
        return view('lbform.draftlist',compact('lists'));
    }
    public function draftedit($id)
    {
        return view('lbform.draftedit',compact('id'));
    }
}
