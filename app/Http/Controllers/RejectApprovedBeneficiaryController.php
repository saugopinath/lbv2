<?php

namespace App\Http\Controllers;

use App\Helpers\CheckAuthHelper;
use App\Models\BeneficiaryCommonList;
use App\Models\Codemaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class RejectApprovedBeneficiaryController extends Controller
{
     public function index()
    {
        // $user = auth()->user();
        // $user->hasRole('Operator');

        // dd('caste modification info');
        // if ($user->hasRole('Operator')) {}

        if(CheckAuthHelper::isApprover()){
            $header = 'Reject Approved Beneficiary Information';
            return view('RejectApprovedBeneficiaryView.reject_approved_beneficiary_index', compact('header'));
        }else{
             $header = 'Opps! you are not able to perform any action';
            return view('CommonRestictedpage\index', compact('header'));
        }
    }
     public function editview(Request $request)
    {
         $header = 'De-Activate Beneficiary Details';
        $application_id = Crypt::decryptString($request->application_id);
        $beneficiary_id = Crypt::decryptString($request->beneficiary_id);
        $reportType=3;
        $BenDetails = BeneficiaryCommonList::where('sourceable_id', $application_id)->with('sourceable')->firstOrFail();
        return view('RejectApprovedBeneficiaryView.reject_approved_beneficiary_processed', compact('application_id', 'header', 'reportType'));
    }
    public function deActiveBeneficiary(Request $request)
    {
        dd('ok');
        // dd($request->all());
    }
}
