<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Services\ElasticsearchService;
use App\Models\AcceptRejectInfo;
class IndexAcceptRejectInfoElasticSearchJob implements ShouldQueue
{
    protected $accept_reject_info;

    /**
     * Create a new job instance.
     */
    public function __construct(AcceptRejectInfo $accept_reject_info)
    {
       $this->accept_reject_info = $accept_reject_info;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
         $elasticsearchService=new ElasticsearchService();
         $elasticsearchService->populateIndex('accept_reject_info', $this->accept_reject_info->toArray());
    }
}
