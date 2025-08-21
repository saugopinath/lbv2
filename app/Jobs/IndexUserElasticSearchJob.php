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
       
        $elasticsearchService=new ElasticsearchService();
        $elasticsearchService->populateIndex('users', $this->user->toArray());
    }
}
