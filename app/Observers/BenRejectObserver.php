<?php

namespace App\Observers;

use App\Models\BeneficiaryCommonList;
use App\Models\BenRejectDetails;
use App\Models\BeneficiaryAadhaar;
use App\Models\DraftBeneficiaryBank;
use App\Models\DraftBeneficiaryContact;
use App\Models\DraftBeneficiaryPersonal;
use App\Models\DraftBeneficiaryDeclaration;
use App\Models\ApplicantRejectRevertDetails;
use App\Models\DraftBeneficiaryRelationship;

class BenRejectObserver
{
    /**
     * Handle the BenRejectDetails "created" event.
     */
    public function creating(BenRejectDetails $benRejectDetails): void
    {

        // dd($benRejectDetails);
        $applicationId = $benRejectDetails->application_id;
        $currentUserId = auth()->id(); // or pass via controller

        // Fetch draft data
        $personal = DraftBeneficiaryPersonal::where('application_id', $applicationId)->first();
        $personal->next_level_role_id = -1;
        $contact = DraftBeneficiaryContact::where('application_id', $applicationId)->first();
        $bank = DraftBeneficiaryBank::where('application_id', $applicationId)->first();
        $declaration = DraftBeneficiaryDeclaration::where('application_id', $applicationId)->first();
        $relationship = DraftBeneficiaryRelationship::where('application_id', $applicationId)->get();
        $aadhar = BeneficiaryAadhaar::where('application_id', $applicationId)->get();

        // Fill the reject record (will be saved automatically)
        $benRejectDetails->beneficiary_id = $personal?->beneficiary_id;
        $benRejectDetails->created_by = $currentUserId;
        $benRejectDetails->personal_details = $personal?->toArray();
        $benRejectDetails->contact_details = $contact?->toArray();
        $benRejectDetails->bank_details = $bank?->toArray();
        $benRejectDetails->declaration_details = $declaration?->toArray();
        $benRejectDetails->relationship_details = $relationship?->toArray();
        $benRejectDetails->aadhar_details = $aadhar?->toArray();
        $benRejectDetails->district_id = $personal?->district_id;
        $benRejectDetails->block_id = $personal?->block_id;
        $benRejectDetails->sub_division_id = $personal?->sub_division_id;
        $benRejectDetails->municipality_id = $personal?->municipality_id;
        $benRejectDetails->ward_id = $personal?->ward_id;
        $benRejectDetails->panchayat_id = $personal?->panchayat_id;


        // Delete data from draft tables
        DraftBeneficiaryPersonal::where('application_id', $applicationId)->delete();




        // dd($benRejectDetails);


        // ApplicantRejectRevertDetails::create([
        //     'application_id' => $applicationId,
        //     'created_by' => $currentUserId,
        //     'reject_revert_reason_id' => $benRejectDetails->reject_revert_reason_id, // or pass
        //     'remark' => $benRejectDetails->remark,
        // ]);
    }


    /**
     * Handle the BenRejectDetails "updated" event.
     */
    public function created(BenRejectDetails $benRejectDetails): void
    {
        try {
            $beneficiaryCommonList = BeneficiaryCommonList::where('sourceable_id', $benRejectDetails->application_id)->first();
            $beneficiaryCommonList->update([
                'sourceable_type' => BenRejectDetails::class
            ]);

        } catch (\Exception $e) {
            \Log::error("Error in BenRejectObserver: " . $e->getMessage());


        }
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