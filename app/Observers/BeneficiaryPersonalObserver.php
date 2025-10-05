<?php

namespace App\Observers;

use App\Models\UniqueAppBenId;
use App\Models\BeneficiaryBank;
use App\Models\BeneficiaryContact;
use Illuminate\Support\Facades\DB;
use App\Models\BeneficiaryPersonal;
use Illuminate\Support\Facades\Log;
use App\Models\DraftBeneficiaryBank;
use App\Models\BeneficiaryCommonList;
use App\Models\BeneficiaryDeclaration;
use App\Models\BeneficiaryRelationship;
use App\Models\DraftBeneficiaryContact;
use App\Models\DraftBeneficiaryPersonal;
use App\Models\DraftBeneficiaryDeclaration;
use App\Models\DraftBeneficiaryRelationship;

class BeneficiaryPersonalObserver
{
    /**
     * Handle the BeneficiaryPersonal "created" event.
     */

    public function creating(BeneficiaryPersonal $beneficiaryPersonal): void
    {
        
    }

    public function created(BeneficiaryPersonal $beneficiaryPersonal): void
    {
    //    $beneficiaryPersonal = DraftBeneficiaryPersonal::find($beneficiaryPersonal->application_id);
    //     $beneficiaryPersonal->delete();
    }

    /**
     * Handle the BeneficiaryPersonal "updated" event.
     */
    public function updated(BeneficiaryPersonal $beneficiaryPersonal): void
    {
        //
    }

    /**
     * Handle the BeneficiaryPersonal "deleted" event.
     */
    public function deleted(BeneficiaryPersonal $beneficiaryPersonal): void
    {
        //
    }

    /**
     * Handle the BeneficiaryPersonal "restored" event.
     */
    public function restored(BeneficiaryPersonal $beneficiaryPersonal): void
    {
        //
    }

    /**
     * Handle the BeneficiaryPersonal "force deleted" event.
     */
    public function forceDeleted(BeneficiaryPersonal $beneficiaryPersonal): void
    {
        //
    }
}
