<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DesignController extends Controller
{
    public function __construct( ) {

    }

    public function tableDesign()
    {
        // dd('bhjcv');
        return view('DesignPages.tableview');
    }
     public function selectionDesign()
    {
        // dd('bhjcv');
        return view('DesignPages.selectionview');
    }

    public function viewPage()
    {
        return view('DesignPages.viewpage');
    }
}