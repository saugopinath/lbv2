<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\EncryptionArray;
use Illuminate\Support\Facades\Crypt;

class BeneficiaryListController extends Controller
{
    public function index()
    {
        return view('beneficiaries.index');
    }

    public function show(Request $request)
    {
        $login_type = 'state_office';
        $reportType = $request->input('report_type');

        // // dd($reportType);
        // $helper = EncryptionArray::lgdsession();
        // // dd($helper);

        // $decrypted = array_map(function ($value) {
        //     return decrypt($value);
        // }, $helper);

        // dd($decrypted);


        return view('beneficiaries.report', compact('reportType', 'login_type'));
    }

    public function storeAssocArrayInSession()
    {
        $lgd_session = [
            // 'state_id' => Crypt::encrypt('19'),
            'district_id' => Crypt::encrypt('305'),
            // 'subdivision_id' => Crypt::encrypt('33903'),
            // 'block_id' => Crypt::encrypt('2793'),
        ];

        session(['lgd_session' => $lgd_session]);
    }
}
