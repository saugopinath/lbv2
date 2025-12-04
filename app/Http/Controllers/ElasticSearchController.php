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
        
         $indexName='accept_reject_infos';
         $elasticsearchService1=new ElasticsearchService();
         //dd($elasticsearchService->verifyIndex($indexName));
         /*if(!$elasticsearchService->verifyIndex($indexName)){
           $elasticsearchService->createIndex($indexName);
         }
         $elasticsearchService->populateIndex($indexName, $accept_reject_info);*/
       //  $elasticsearchService->populateIndex($indexName, $accept_reject_info);
         dd($elasticsearchService1->getPaginatedIndexData($indexName));
       
    }
}
