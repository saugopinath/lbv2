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

        // $login_type = 'state_office';
        $select_lgd = session('lgd_session');
        $filter_condition = [];

        if ($select_lgd) {
            foreach ($select_lgd as $key => $val) {
                try {
                    $filter_condition[$key] = Crypt::decryptString($val);
                } catch (\Exception $e) {
                    $filter_condition[$key] = $val;
                }
            }
        }

        $login_type = $filter_condition['office_type_id'] ?? null;


        // dd($login_type);
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
