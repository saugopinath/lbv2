<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LBFormController extends Controller
{
protected $login_type;
public function __construct( ) {

      $this->login_type = 'block_office';
      $this->block_code = 2978;
      $this->subdivision_code = 34401;

    }



     public function index()

    {
        $login_type = $this->login_type; // use it here
        return view('LBForm.SubmittedList', compact('login_type'));
}
}
