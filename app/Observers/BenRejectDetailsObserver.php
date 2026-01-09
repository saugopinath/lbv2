<?php

namespace App\Observers;

use App\Models\BeneficiaryAadhaar;
use App\Models\BenRejectDetails;
use App\Models\DraftBeneficiaryPersonal;

class BenRejectDetailsObserver
{
    /**
     * Handle the BenRejectDetails "created" event.
     */
    public function creating(BenRejectDetails $benRejectDetails): void {}


    /**
     * Handle the BenRejectDetails "updated" event.
     */
    public function created(BenRejectDetails $benRejectDetails): void
    {
        // $DraftBeneficiaryPersonal = DraftBeneficiaryPersonal::find($benRejectDetails->application_id);
        // $DraftBeneficiaryPersonal->delete();
        // $BeneficiaryAadhaar = BeneficiaryAadhaar::find($benRejectDetails->application_id);
        // $BeneficiaryAadhaar->delete();
    }
    public function updated(BenRejectDetails $benRejectDetails): void
    {
        //
    }

    /**
     * Handle the BenRejectDetails "deleted" event.
     */
    public function deleted(BenRejectDetails $benRejectDetails): void
    {
        //
    }

    /**
     * Handle the BenRejectDetails "restored" event.
     */
    public function restored(BenRejectDetails $benRejectDetails): void
    {
        //
    }

    /**
     * Handle the BenRejectDetails "force deleted" event.
     */
    public function forceDeleted(BenRejectDetails $benRejectDetails): void
    {
        //
    }
}
