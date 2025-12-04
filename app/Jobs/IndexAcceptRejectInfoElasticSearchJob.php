<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Services\ElasticsearchService;
use App\Models\Codemaster;
use App\Models\AcceptRejectInfo;
use Illuminate\Support\Facades\Auth;
class IndexAcceptRejectInfoElasticSearchJob implements ShouldQueue
{
    protected $accept_reject_info;

    /**
     * Create a new job instance.
     */
    public function __construct(AcceptRejectInfo $accept_reject_info)
    {
       $user = auth()->user();
       $accept_reject_info->user_details = $user->toArray();
       $accept_reject_info->action_description = Codemaster::select('name')->where('code',$accept_reject_info->op_type)->first()->toArray();
       $this->accept_reject_info = $accept_reject_info;
       //dd( $this->accept_reject_info->toArray());
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $indexName='accept_reject_infos';
        $elasticsearchService=new ElasticsearchService();
        if(!$elasticsearchService->verifyIndex($indexName)){
           $elasticsearchService->createIndex($indexName);
         }
         //dd( $this->accept_reject_info->toArray());
         $elasticsearchService->populateIndex('accept_reject_infos', $this->accept_reject_info->toArray());
    }
}
