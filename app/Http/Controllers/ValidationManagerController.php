<?php

namespace App\Http\Controllers;

use App\Models\MasterTab;
use App\Models\Scheme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class ValidationManagerController extends Controller
{
    public function index(Request $request)
    {       
        [$schemeId, $tabCode] =
            explode('|', Crypt::decryptString($request->ref));           
        $header='Manage Defult Field Validations';
        $schemeName = Scheme::where('id', $schemeId)->value('name');
        $tabName=MasterTab::where('tab_code',$tabCode)->value('tab_name');

        return view('ValidationManager.index', [
            'schemeId' =>$schemeId,
            'tabCode'  =>$tabCode,
            'header'   =>$header,
            'schemeName'=>$schemeName,
            'tabName'=>$tabName,
        ]);
    }
}
