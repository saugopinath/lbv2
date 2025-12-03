<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Services\ElasticsearchService;
use App\Models\User;

class IndexUserElasticSearchJob
{

    protected $user;
    /**
     * Create a new job instance.
     */


    public function __construct(User $user)
    {
        //  dd('inside job');
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
     public function handle(): void
     {
         $indexName='users';
         $elasticsearchService=new ElasticsearchService();
         if(!$elasticsearchService->verifyIndex($indexName)){
           $elasticsearchService->createIndex($indexName);
         }
         $elasticsearchService->populateIndex('users', $this->user->toArray());
     }
}
