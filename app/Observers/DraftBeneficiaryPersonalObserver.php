<?php

namespace App\Observers;

use App\Models\DraftBeneficiaryPersonal;
use App\Models\BeneficiaryPersonal;
use App\Models\BeneficiaryCommonList;
use App\Models\BeneficiaryBank;
use App\Models\AcceptRejectInfo;
use App\Models\BeneficiaryContact;
use App\Models\BeneficiaryRelationship;
use App\Models\Codemaster;
use App\Models\BeneficiaryDeclaration;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;

class DraftBeneficiaryPersonalObserver
{
    /**
     * Handle the DraftBeneficiaryPersonal "created" event.
     */
    public function created(DraftBeneficiaryPersonal $draftBeneficiaryPersonal): void
    {
        //
    }

    /**
     * Handle the DraftBeneficiaryPersonal "updated" event.
     */
    public function updated(DraftBeneficiaryPersonal $draftBeneficiaryPersonal): void
    {
        //
    }

    /**
     * Handle the DraftBeneficiaryPersonal "deleted" event.
     */
    public function deleted(DraftBeneficiaryPersonal $draftBeneficiaryPersonal): void
    {
        //
    }

    /**
     * Handle the DraftBeneficiaryPersonal "restored" event.
     */
    public function restored(DraftBeneficiaryPersonal $draftBeneficiaryPersonal): void
    {
        //
    }

    /**
     * Handle the DraftBeneficiaryPersonal "force deleted" event.
     */
    public function forceDeleted(DraftBeneficiaryPersonal $draftBeneficiaryPersonal): void
    {
        //
    }
    public function deleting(DraftBeneficiaryPersonal $draft)
    {
        // dd($draft);
        $beneficiary = BeneficiaryPersonal::updateOrCreate(
            ['application_id' => $draft->application_id],
            collect($draft)->except(['id', 'created_at', 'updated_at'])->merge(['next_level_role_id' => Codemaster::getIdByCode(2303)])->toArray()
        );
        if ($draft->contact) {
            BeneficiaryContact::updateOrCreate(
                ['application_id' => $beneficiary->application_id],
                collect($draft->contact)->except(['created_at', 'updated_at'])->merge(['beneficiary_id' => $beneficiary->beneficiary_id])->toArray()
            );
        }
        if ($draft->relationships) {
            foreach ($draft->relationships as $rel) {
                BeneficiaryRelationship::updateOrCreate(
                    ['application_id' => $beneficiary->application_id],
                    collect($rel)->except(['created_at', 'updated_at'])->merge(['beneficiary_id' => $beneficiary->beneficiary_id])->toArray()
                );
            }
        }
        if ($draft->bank) {
            BeneficiaryBank::updateOrCreate(
                ['application_id' => $beneficiary->application_id],
                collect($draft->bank)->except(['created_at', 'updated_at'])->merge(['beneficiary_id' => $beneficiary->beneficiary_id])->toArray()
            );
        }
        if ($draft->declaration) {
            BeneficiaryDeclaration::updateOrCreate(
                ['application_id' => $beneficiary->application_id],
                collect($draft->declaration)->except(['created_at', 'updated_at'])->merge(['beneficiary_id' => $beneficiary->beneficiary_id])->toArray()
            );
        }
        // BeneficiaryCommonList::where('sourceable_type', DraftBeneficiaryPersonal::class)
        //     ->where('sourceable_id', $draft->application_id)
        //     ->update([
        //         'sourceable_type' => BeneficiaryPersonal::class,
        //         'sourceable_id'   => $beneficiary->application_id,
        //     ]);
        
    }
}
