<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Crypt;

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
        $button_show = 1;
        return view('lbform.draftlist',compact('button_show'));
    }
    public function draftedit($id)
    {
        $id= Crypt::decryptString($id);
        return view('lbform.draftedit',compact('id'));
    }
}
