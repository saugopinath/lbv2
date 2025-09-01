<?php

namespace App\Observers;

use App\Models\BeneficiaryBank;
use App\Models\BenRejectDetails;
use App\Models\BeneficiaryAadhaar;
use App\Models\BeneficiaryContact;
use App\Models\BeneficiaryPersonal;
use App\Models\DraftBeneficiaryBank;
use App\Models\BeneficiaryCommonList;
use App\Models\FaultyBeneficiaryBank;
use App\Models\BeneficiaryDeclaration;
use App\Models\BeneficiaryRelationship;
use App\Models\DraftBeneficiaryContact;
use App\Models\FaultyBenficiaryContact;
use App\Models\DraftBeneficiaryPersonal;
use App\Models\FaultyBeneficiaryPersonal;
use App\Models\DraftBeneficiaryDeclaration;
use App\Models\FaultyBenficiaryDecleration;
use App\Models\ApplicantRejectRevertDetails;
use App\Models\DraftBeneficiaryRelationship;
use App\Models\FaultyBenficiaryRelationship;

class BenRejectDetailsObserver
{
    /**
     * Handle the BenRejectDetails "created" event.
     */
    public function creating(BenRejectDetails $benRejectDetails): void
    {

        // dd($benRejectDetails);
        $applicationId = $benRejectDetails->application_id;
        $currentUserId = auth()->id();

        // Fetch draft data
        switch ($benRejectDetails->update_code) {

            case 1: // Draft Beneficiary
                // dd('ok');
                $personal = DraftBeneficiaryPersonal::where('application_id', $applicationId)->first();
                $contact = DraftBeneficiaryContact::where('application_id', $applicationId)->first();
                $bank = DraftBeneficiaryBank::where('application_id', $applicationId)->first();
                $declaration = DraftBeneficiaryDeclaration::where('application_id', $applicationId)->first();
                $relationship = DraftBeneficiaryRelationship::where('application_id', $applicationId)->get();
                $aadhar = BeneficiaryAadhaar::where('application_id', $applicationId)->get();


                DraftBeneficiaryPersonal::where('application_id', $applicationId)->delete();
                BeneficiaryAadhaar::where('application_id', $applicationId)->delete();
                break;

            case 2: // Approved Beneficiary
                // dd('ok1');
                $personal = BeneficiaryPersonal::where('application_id', $applicationId)->first();
                $contact = BeneficiaryContact::where('application_id', $applicationId)->first();
                $bank = BeneficiaryBank::where('application_id', $applicationId)->first();
                $declaration = BeneficiaryDeclaration::where('application_id', $applicationId)->first();
                $relationship = BeneficiaryRelationship::where('application_id', $applicationId)->get();
                $aadhar = BeneficiaryAadhaar::where('application_id', $applicationId)->get();

                BeneficiaryPersonal::where('application_id', $applicationId)->delete();
                BeneficiaryAadhaar::where('application_id', $applicationId)->delete();
                break;

            case 3: // Faulty Beneficiary
                //  dd('ok2');
                $personal = FaultyBeneficiaryPersonal::where('application_id', $applicationId)->first();
                $contact = FaultyBenficiaryContact::where('application_id', $applicationId)->first();
                $bank = FaultyBeneficiaryBank::where('application_id', $applicationId)->first();
                $declaration = FaultyBenficiaryDecleration::where('application_id', $applicationId)->first();
                $relationship = FaultyBenficiaryRelationship::where('application_id', $applicationId)->get();
                $aadhar = BeneficiaryAadhaar::where('application_id', $applicationId)->get();

                FaultyBeneficiaryPersonal::where('application_id', $applicationId)->delete();
                BeneficiaryAadhaar::where('application_id', $applicationId)->delete();
                break;

            default:
                $personal = $contact = $bank = $declaration = null;
                $relationship = collect();
                $aadhar = collect();
        }
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
