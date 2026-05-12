<?php

namespace App\Http\Controllers;

use App\Models\BeneficiaryPersonalDetail;
use App\Models\WorkflowsteproleMapping;
use Illuminate\Support\Facades\Request;

class testController extends Controller
{
    public function index(Request $request)
    {

        $beneficiaries = BeneficiaryPersonalDetail::query()
            ->where('scheme_id', 20)
            ->where('next_level_role_id', 2)
            ->whereHas('aadhar', function ($q) {
                $q->whereNull('encoded_aadhar');
            })
            ->select([
                'application_id',
                'beneficiary_id',
                'scheme_id',
            ])
            ->get();

        dd($beneficiaries);
    }
}