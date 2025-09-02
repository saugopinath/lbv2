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
        $draftPersonal = DraftBeneficiaryPersonal::where('application_id', $beneficiaryPersonal->application_id)->first();

        if (!$draftPersonal) {
            throw new \Exception("Draft personal not found for application_id: {$beneficiaryPersonal->application_id}");
        }

        $uniqueBen = UniqueAppBenId::where('application_id', $beneficiaryPersonal->application_id)->first();
        if (!$uniqueBen) {
            throw new \Exception("Unique beneficiary ID not found for application_id: {$beneficiaryPersonal->application_id}");
        }

        // Assign all required fields before insert
        $beneficiaryPersonal->beneficiary_id = $uniqueBen->beneficiary_id;
        $beneficiaryPersonal->full_name = $draftPersonal->full_name;
        $beneficiaryPersonal->dob = $draftPersonal->dob;
        $beneficiaryPersonal->mobile_no = $draftPersonal->mobile_no;
        $beneficiaryPersonal->email = $draftPersonal->email;
        $beneficiaryPersonal->caste = $draftPersonal->caste;
        $beneficiaryPersonal->marital_status = $draftPersonal->marital_status;
        $beneficiaryPersonal->entry_type = $draftPersonal->entry_type;
        $beneficiaryPersonal->next_level_role_id = $draftPersonal->next_level_role_id;
        $beneficiaryPersonal->is_final_submit = $draftPersonal->is_final_submit;
        $beneficiaryPersonal->is_faulty = $draftPersonal->is_faulty;
        $beneficiaryPersonal->ds_date = $draftPersonal->ds_date;
        $beneficiaryPersonal->ds_registration_no = $draftPersonal->ds_registration_no;
        $beneficiaryPersonal->created_by = $draftPersonal->created_by;
        $beneficiaryPersonal->district_id = $draftPersonal->district_id;
        $beneficiaryPersonal->block_id = $draftPersonal->block_id;
        $beneficiaryPersonal->sub_division_id = $draftPersonal->sub_division_id;
        $beneficiaryPersonal->municipality_id = $draftPersonal->municipality_id;
        $beneficiaryPersonal->ward_id = $draftPersonal->ward_id;
        $beneficiaryPersonal->panchayat_id = $draftPersonal->panchayat_id;
        // dd($beneficiaryPersonal);
    }

    public function created(BeneficiaryPersonal $beneficiaryPersonal): void
    {
        DB::transaction(function () use ($beneficiaryPersonal) {

            try {
                $beneficiaryId = $beneficiaryPersonal->beneficiary_id;


                $draftPersonal = DraftBeneficiaryPersonal::where('application_id', $beneficiaryPersonal->application_id)->first();
                // dd($draftPersonal);
                if ($draftPersonal) {
                    $beneficiaryPersonal->update([

                        'full_name' => $draftPersonal->full_name,
                        'dob' => $draftPersonal->dob,
                        'mobile_no' => $draftPersonal->mobile_no,
                        'email' => $draftPersonal->email,
                        'caste' => $draftPersonal->caste,
                        'marital_status' => $draftPersonal->marital_status,
                        'entry_type' => $draftPersonal->entry_type,
                        'next_level_role_id' => $draftPersonal->next_level_role_id,
                        'is_final_submit' => $draftPersonal->is_final_submit,
                        'is_faulty' => $draftPersonal->is_faulty,
                        'ds_date' => $draftPersonal->ds_date,
                        'ds_registration_no' => $draftPersonal->ds_registration_no,
                        'created_by' => $draftPersonal->created_by,
                        'district_id' => $draftPersonal->district_id,
                        'block_id' => $draftPersonal->block_id,
                        'sub_division_id' => $draftPersonal->sub_division_id,
                        'municipality_id' => $draftPersonal->municipality_id,
                        'ward_id' => $draftPersonal->ward_id,
                        'panchayat_id' => $draftPersonal->panchayat_id,
                    ]);

                }


                $draftBank = DraftBeneficiaryBank::where('application_id', $beneficiaryPersonal->application_id)->first();
                if ($draftBank) {
                    BeneficiaryBank::create([
                        'application_id' => $beneficiaryPersonal->application_id,
                        'beneficiary_id' => $beneficiaryId,
                        'created_by' => $draftBank->created_by,
                        'ifsc' => $draftBank->ifsc,
                        'bank_account_number' => $draftBank->bank_account_number,
                    ]);
                    $draftBank->delete();
                }

                $draftContact = DraftBeneficiaryContact::where('application_id', $beneficiaryPersonal->application_id)->first();
                if ($draftContact) {
                    // dd($draftContact);
                    BeneficiaryContact::create([
                        'application_id' => $beneficiaryPersonal->application_id,
                        'beneficiary_id' => $beneficiaryId,
                        'district_id' => $draftContact->district_id,
                        'rural_urban_id' => $draftContact->rural_urban_id,
                        'block_id' => $draftContact->block_id,
                        'sub_division_id' => $draftContact->sub_division_id,
                        'municipality_id' => $draftContact->municipality_id,
                        'ward_id' => $draftContact->ward_id,
                        'panchayat_id' => $draftContact->panchayat_id,
                        'police_station' => $draftContact->police_station,
                        'village_town_city' => $draftContact->village_town_city,
                        'house_premise_no' => $draftContact->house_premise_no,
                        'post_office' => $draftContact->post_office,
                        'pincode' => $draftContact->pincode,
                        'residency_period' => $draftContact->residency_period,
                        'created_by' => $draftContact->created_by,
                    ]);
                    $draftContact->delete();
                }


                $draftDeclaration = DraftBeneficiaryDeclaration::where('application_id', $beneficiaryPersonal->application_id)->first();
                if ($draftDeclaration) {
                    // dd($draftDeclaration);
                    BeneficiaryDeclaration::create([
                        'application_id' => $beneficiaryPersonal->application_id,
                        'beneficiary_id' => $beneficiaryId,
                        'created_by' => $draftDeclaration->created_by,
                        'is_resident' => $draftDeclaration->is_resident,
                        'earn_monthly_remuneration' => $draftDeclaration->earn_monthly_remuneration,
                        'info_genuine_decl' => $draftDeclaration->info_genuine_decl,
                        'av_status' => $draftDeclaration->av_status,
                    ]);
                    $draftDeclaration->delete();
                }

                // ----- Move Relationship Data -----
                $draftRelationships = DraftBeneficiaryRelationship::where('application_id', $beneficiaryPersonal->application_id)->get();
                foreach ($draftRelationships as $draftRel) {
                    BeneficiaryRelationship::create([
                        'application_id' => $beneficiaryPersonal->application_id,
                        'beneficiary_id' => $beneficiaryId,
                        'full_name' => $draftRel->full_name,
                        'relation_type_id' => $draftRel->relation_type_id,
                        'created_by' => $draftRel->created_by,
                    ]);
                    $draftRel->delete();
                }

                $beneficiaryCommonList = BeneficiaryCommonList::where('sourceable_id', $beneficiaryPersonal->application_id)->first();
                if ($beneficiaryCommonList) {
                    $beneficiaryCommonList->update([
                        'sourceable_type' => BeneficiaryPersonal::class
                    ]);
                    if ($draftPersonal)
                        $draftPersonal->delete();
                }

            } catch (\Exception $e) {
                Log::error("Error moving draft data to beneficiary tables: " . $e->getMessage());
                throw $e;
            }

        });
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
