<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\BeneficiaryAadhaar;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use App\Helpers\AadhaarHelper;
use App\Helpers\WorkFlowPermissionHelper;
use App\Attributes\Loggable;
use Illuminate\Support\Facades\Session;
use App\Contracts\AadhaarEncryptionServiceInterface;

class DupAadhaarCheckV2 extends Component
{
    public $aadhaar;
    public $error = null;
    public $schemeId;
    public $showDsTable = false;

    #[Loggable(level: 'Moderate', nickname: 'Check Aadhaar Duplication')]
    public function mount($schemeId = null)
    {
        $this->schemeId = $schemeId;
    }
    public function checkDuplicate()
    {
        $this->error = null;
        $this->aadhaar = trim($this->aadhaar);

        // Commented for Dev
        // if (!AadhaarHelper::validate($this->aadhaar)) { // Verhoeff checksum validation
        //     $this->error = "Invalid Aadhaar number";
        //     $this->dispatch('hideLoader');
        //     return ['status' => 'error', 'message' => $this->error];
        // }

        // Previous work, which was replaced by the work done below it
        // $encoded_aadhar = Crypt::encryptString($this->aadhaar);
        // $aadhaar_hash = md5($this->aadhaar);

        $encrypted_aadhaar = app(AadhaarEncryptionServiceInterface::class)->generateEncryptedAadhaar($this->aadhaar);

        $exists = BeneficiaryAadhaar::
            // where('aadhaar_hash', $aadhaar_hash) // Previous work, which was replaced by the work done below it
            where('aadhaar_token', $encrypted_aadhaar)
            ->where('scheme_id', $this->schemeId)
            ->exists();

        if ($exists) {
            $this->error = "Duplicate Aadhaar found for this scheme!";
            $this->dispatch('hideLoader');
            return [
                'status' => 'duplicate',
                'message' => $this->error,
                'ds_entry' => '',
            ];
        }

        $this->dispatch('aadhaarChecked', [
            // 'encoded' => $encoded_aadhar, // VV
            // 'hash' => $aadhaar_hash // Previous work, which was replaced by the work done below it
            'aadhaar_token' => $encrypted_aadhaar
        ]);

        $this->dispatch('hideLoader');

        return ['status' => 'success', 'message' => '✅ Aadhaar is valid and not duplicate.'];
    }
    public function DsMark()
    {
        // Session::put('dup_aadhaar', md5(trim($this->aadhaar)));
        $this->showDsTable = true;
        $encrypted_aadhaar = app(AadhaarEncryptionServiceInterface::class)->generateEncryptedAadhaar($this->aadhaar);
        $this->dispatch('aadhaarCheckedds', [
            // 'aadhar_hash' => md5(trim($this->aadhaar)) // Previous work, which was replaced by the work done below it
            'aadhaar_token' => $encrypted_aadhaar
        ]);
        return [
            'status' => true
        ];
    }
    public function render()
    {
        return view('livewire.dup-aadhaar-check-v2');
    }
}
