<?php

namespace App\Http\Controllers;

use App\Models\WorkflowsteproleMapping;
use Illuminate\Support\Facades\Request;

class testController extends Controller
{
    public function index(Request $request)
    {

        $totalApproved = DB::connection('pgsql_app_read')
            ->table('pension.beneficiary_personals')
            ->where('scheme_id', $role20)
            ->count();
    }
}