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
use App\Models\BenRejectDetails;
use App\Models\DraftBeneficiaryContact;
use App\Models\DraftBeneficiaryBank;
use App\Models\DraftBeneficiaryDeclaration;
use App\Models\DraftBeneficiaryRelationship;
use App\Models\BeneficiaryAadhaar;

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
        if ($draftBeneficiaryPersonal->wasChanged('next_level_role_id')) {
            $newRole = $draftBeneficiaryPersonal->next_level_role_id;
            if ($newRole == Codemaster::getIdByCode(0)) {
                // dd($draftBeneficiaryPersonal);
                // $beneficiary = BeneficiaryPersonal::updateOrCreate(
                //     ['application_id' => $draftBeneficiaryPersonal->application_id],
                //     collect($draftBeneficiaryPersonal)->except(['id', 'created_at', 'updated_at'])->toArray()
                // );
                $data = $draftBeneficiaryPersonal->getAttributes();
                unset($data['id'], $data['created_at'], $data['updated_at']);
                $beneficiary = BeneficiaryPersonal::updateOrCreate(
                    ['application_id' => $draftBeneficiaryPersonal->application_id],
                    $data
                );
                if ($draftBeneficiaryPersonal->contact) {
                    BeneficiaryContact::updateOrCreate(
                        ['application_id' => $beneficiary->application_id],
                        collect($draftBeneficiaryPersonal->contact)->except(['created_at', 'updated_at'])->merge(['beneficiary_id' => $beneficiary->beneficiary_id])->toArray()
                    );
                }
                foreach ($draftBeneficiaryPersonal->relationships as $rel) {
                    BeneficiaryRelationship::updateOrCreate(
                        [
                            'application_id' => $beneficiary->application_id,
                            'relation_type_id' => $rel['relation_type_id']
                        ],
                        collect($rel)
                            ->except(['created_at', 'updated_at','id'])
                            ->merge(['beneficiary_id' => $beneficiary->beneficiary_id])
                            ->toArray()
                    );
                }
                if ($draftBeneficiaryPersonal->bank) {
                    BeneficiaryBank::updateOrCreate(
                        ['application_id' => $beneficiary->application_id],
                        collect($draftBeneficiaryPersonal->bank)->except(['created_at', 'updated_at'])->merge(['beneficiary_id' => $beneficiary->beneficiary_id])->toArray()
                    );
                }
                if ($draftBeneficiaryPersonal->declaration) {
                    BeneficiaryDeclaration::updateOrCreate(
                        ['application_id' => $beneficiary->application_id],
                        collect($draftBeneficiaryPersonal->declaration)->except(['created_at', 'updated_at'])->merge(['beneficiary_id' => $beneficiary->beneficiary_id])->toArray()
                    );
                }
                $beneficiaryPersonal = DraftBeneficiaryPersonal::find($draftBeneficiaryPersonal->application_id);
                $beneficiaryPersonal->delete();
            } elseif ($newRole == Codemaster::getIdByCode(-1)) {
                $select_lgd = session('lgd_session');
                $benrej = new BenRejectDetails;
                $benrej->application_id     = $draftBeneficiaryPersonal->application_id;
                $benrej->created_by     = Auth::id();
                $benrej->district_id     = Crypt::decryptString($select_lgd['district_id']);
                $benrej->personal_details     = DraftBeneficiaryPersonal::where('application_id', $draftBeneficiaryPersonal->application_id)->get()->toArray();
                $benrej->contact_details      = DraftBeneficiaryContact::where('application_id', $draftBeneficiaryPersonal->application_id)->get()->toArray();
                $benrej->bank_details         = DraftBeneficiaryBank::where('application_id', $draftBeneficiaryPersonal->application_id)->get()->toArray();
                $benrej->declaration_details  = DraftBeneficiaryDeclaration::where('application_id', $draftBeneficiaryPersonal->application_id)->get()->toArray();
                $benrej->relationship_details = DraftBeneficiaryRelationship::where('application_id', $draftBeneficiaryPersonal->application_id)->get()->toArray();
                $benrej->aadhar_details       = BeneficiaryAadhaar::where('application_id', $draftBeneficiaryPersonal->application_id)->get()->toArray();
                $benrej->save();
                $DraftBeneficiaryPersonal = DraftBeneficiaryPersonal::find($draftBeneficiaryPersonal->application_id);
                $DraftBeneficiaryPersonal->delete();
                $BeneficiaryAadhaar = BeneficiaryAadhaar::find($draftBeneficiaryPersonal->application_id);
                $BeneficiaryAadhaar->delete();
            }
        }
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
    public function deleting(DraftBeneficiaryPersonal $draftBeneficiaryPersonal)
    {
        // dd($draft);

        // BeneficiaryCommonList::where('sourceable_type', DraftBeneficiaryPersonal::class)
        //     ->where('sourceable_id', $draft->application_id)
        //     ->update([
        //         'sourceable_type' => BeneficiaryPersonal::class,
        //         'sourceable_id'   => $beneficiary->application_id,
        //     ]);

    }
}
