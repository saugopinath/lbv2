<?php

namespace App\Http\Controllers;

use App\Helpers\CheckAuthHelper;
use Illuminate\Http\Request;

class MarkedUpdateBeneficiary extends Controller
{
    public function index()
    {
         if (CheckAuthHelper::isHOD()) {
            $header = 'Marked Beneficiary To Update Informations';
            return view('MarkedUpdateBeneficiary.index', compact('header'));
        } else {
            $header = 'Opps! you are not able to perform any action';
            return view('CommonRestictedpage\index', compact('header'));
        }
    }
}
