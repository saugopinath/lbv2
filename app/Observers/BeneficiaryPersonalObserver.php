<?php

namespace App\Observers;

use App\Models\BeneficiaryAadhaar;
use App\Models\UniqueAppBenId;
use App\Models\BeneficiaryBank;
use App\Models\BeneficiaryContact;
use Illuminate\Support\Facades\DB;
use App\Models\BeneficiaryPersonal;
use Illuminate\Support\Facades\Log;
// use App\Models\DraftBeneficiaryBank;
use App\Models\BeneficiaryCommonList;
use App\Models\BeneficiaryDeclaration;
use App\Models\BeneficiaryRelationship;
use App\Models\BenRejectDetails;
use App\Models\Codemaster;
// use App\Models\DraftBeneficiaryContact;
use App\Models\DraftBeneficiaryPersonal;
// use App\Models\DraftBeneficiaryDeclaration;
// use App\Models\DraftBeneficiaryRelationship;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class BeneficiaryPersonalObserver
{
    /**
     * Handle the BeneficiaryPersonal "created" event.
     */

    public function creating(BeneficiaryPersonal $beneficiaryPersonal): void {}

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
        if ($beneficiaryPersonal->wasChanged('next_level_role_id')) {
            $newRole = $beneficiaryPersonal->next_level_role_id;
            
                if ($newRole == Codemaster::getIdByCode(-1)) {
                    $select_lgd = session('lgd_session');
                    $benrej = new BenRejectDetails;
                    $benrej->application_id     = $beneficiaryPersonal->application_id;
                    $benrej->created_by     = Auth::id();
                    $benrej->district_id     = Crypt::decryptString($select_lgd['district_id']);
                    $benrej->personal_details     = BeneficiaryPersonal::where('application_id', $beneficiaryPersonal->application_id)->get()->toArray();
                    $benrej->contact_details      = BeneficiaryContact::where('application_id', $beneficiaryPersonal->application_id)->get()->toArray();
                    $benrej->bank_details         = BeneficiaryBank::where('application_id', $beneficiaryPersonal->application_id)->get()->toArray();
                    $benrej->declaration_details  = BeneficiaryDeclaration::where('application_id', $beneficiaryPersonal->application_id)->get()->toArray();
                    $benrej->relationship_details = BeneficiaryRelationship::where('application_id', $beneficiaryPersonal->application_id)->get()->toArray();
                    $benrej->aadhaar_details       = BeneficiaryAadhaar::where('application_id', $beneficiaryPersonal->application_id)->get()->toArray();
                    $benrej->save();

                    $beneficiaryPersonal = BeneficiaryPersonal::find($beneficiaryPersonal->application_id);
                    $beneficiaryPersonal->delete();
                    $BeneficiaryAadhaar = BeneficiaryAadhaar::find($beneficiaryPersonal->application_id);
                    // dd($BeneficiaryAadhaar);
                    $BeneficiaryAadhaar->delete();
                }
            }
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
