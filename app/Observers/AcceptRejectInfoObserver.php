<?php

namespace App\Observers;

use App\Models\AcceptRejectInfo;
use App\Jobs\IndexAcceptRejectInfoElasticSearchJob;
class AcceptRejectInfoObserver
{
    /**
     * Handle the AcceptRejectInfo "created" event.
     */
    public function created(AcceptRejectInfo $acceptRejectInfo): void
    {
         //dd('ok');
         dispatch(new IndexAcceptRejectInfoElasticSearchJob($acceptRejectInfo));
    }

    /**
     * Handle the AcceptRejectInfo "updated" event.
     */
    public function updated(AcceptRejectInfo $acceptRejectInfo): void
    {
        //
    }

    /**
     * Handle the AcceptRejectInfo "deleted" event.
     */
    public function deleted(AcceptRejectInfo $acceptRejectInfo): void
    {
        //
    }

    /**
     * Handle the AcceptRejectInfo "restored" event.
     */
    public function restored(AcceptRejectInfo $acceptRejectInfo): void
    {
        //
    }

    /**
     * Handle the AcceptRejectInfo "force deleted" event.
     */
    public function forceDeleted(AcceptRejectInfo $acceptRejectInfo): void
    {
        //
    }
}
