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


    public function approverIndex(Request $request)
    {
        return view('livewire.annapurna-yojana.approver-index');
    }

    public function approverDetails(Request $request, $family_id)
    {
        return view('livewire.annapurna-yojana.approver-details-page', ['family_id' => $family_id]);
    }
}
