<?php

namespace App\Observers;

use App\Models\DraftBeneficiaryPersonal;
use App\Models\BeneficiaryPersonal;
use App\Models\BeneficiaryCommonList;
use App\Models\BeneficiaryBank;
use App\Models\BeneficiaryContact;
use App\Models\BeneficiaryRelationship;
use App\Models\BeneficiaryDeclaration;
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
        $data = $draft->toArray();
        unset($data['id']);
        $beneficiary = BeneficiaryPersonal::updateOrCreate(
            ['application_id' => $draft->application_id],
            $data
        );
        if ($draft->contact) {
            $contactData = $draft->contact->toArray();
            BeneficiaryContact::updateOrCreate(
                ['application_id' => $beneficiary->application_id],
                $contactData
            );
        }
        if ($draft->relationships) {
            $relationshipData = $draft->relationships->toArray();
            foreach ($relationshipData as $rel) {
                BeneficiaryRelationship::updateOrCreate(
                    ['application_id' => $beneficiary->application_id],
                    $rel
                );
            }
        }
        if ($draft->bank) {
            $bankData = $draft->bank->toArray();
            BeneficiaryBank::updateOrCreate(
                ['application_id' => $beneficiary->application_id],
                $bankData
            );
        }
        if ($draft->declaration) {
            $declarationData = $draft->declaration->toArray();
            BeneficiaryDeclaration::updateOrCreate(
                ['application_id' => $beneficiary->application_id],
                $declarationData
            );
        }
        BeneficiaryCommonList::where('sourceable_type', DraftBeneficiaryPersonal::class)
            ->where('sourceable_id', $draft->application_id)
            ->update([
                'sourceable_type' => BeneficiaryPersonal::class,
                'sourceable_id'   => $beneficiary->application_id,
            ]);
    }
}
