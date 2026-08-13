<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Helpers\CheckAuthHelper;
use Illuminate\Http\Request;
use App\Services\ElasticsearchService;
use App\Models\AcceptRejectInfo;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class ElasticSearchController extends Controller
{
    
    public function index()
    {
         DB::beginTransaction();
         $accept_reject_info = AcceptRejectInfo::create([
                'application_id' => 150000200,
                'beneficiary_id' => 700000200,
                'user_id' =>8,
                'op_type' =>21,
                'created_at' => Carbon::now(),
            ]);
         $indexName='accept_reject_infos';
         $elasticsearchService=new ElasticsearchService();
         //dd($elasticsearchService->verifyIndex($indexName));
         if(!$elasticsearchService->verifyIndex($indexName)){
           $elasticsearchService->createIndex($indexName);
         }
         $elasticsearchService->populateIndex($indexName, $accept_reject_info);
         dd($elasticsearchService->getPaginatedIndexData($indexName));
       
    }
}
