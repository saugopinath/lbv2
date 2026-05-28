<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AnnapurnaYojanaVerificationController extends Controller
{

    public function __construct() {}


    public function verifierIndex(Request $request)
    {
        return view('livewire.annapurna-yojana.verifier-index');
    }

    public function verifierDetails(Request $request, $family_id)
    {
        return view('livewire.annapurna-yojana.verifier-details-page', ['family_id' => $family_id]);
    }
}
