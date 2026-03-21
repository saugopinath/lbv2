<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\BeneficiaryAadhaar;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use App\Helpers\AadhaarHelper;
use App\Helpers\WorkFlowPermissionHelper;
use App\Attributes\Loggable;

class DupAadhaarCheck extends Component
{
    public $aadhaar;
    public $error = null;
    public $schemeId;
    // public function checkDuplicate()
    // {
    //     $this->error = null;
    //     $this->aadhaar = trim($this->aadhaar);
    //     if (!AadhaarHelper::validate($this->aadhaar)) {
    //         $this->error = "Invalid Aadhaar number sc";
    //         $this->dispatch('hideLoader');
    //         return ['status' => 'error', 'message' => $this->error];
    //     }
    //     $encoded_aadhar = Crypt::encryptString($this->aadhaar);
    //     $aadhaar_hash = md5($this->aadhaar);
    //     if (BeneficiaryAadhaar::where('aadhar_hash', $aadhaar_hash)->exists()) {
    //         $this->error = "Duplicate Aadhaar found!";
    //         $this->dispatch('hideLoader');
    //         return ['status' => 'duplicate', 'message' => $this->error];
    //     }       

    //     $this->dispatch('aadhaarChecked', [
    //         'encoded' => $encoded_aadhar,
    //         'hash' => $aadhaar_hash,           
    //     ]);
    //     $this->dispatch('hideLoader');
    //     return ['status' => 'success', 'message' => '✅ Aadhaar is valid and not duplicate.'];
    // }
    #[Loggable(level: 'Moderate', nickname: 'Check Aadhaar Duplication')]
    public function mount($schemeId = null)
    {
        $this->schemeId = $schemeId;
    }
    public function checkDuplicate()
    {
        $this->error = null;
        $this->aadhaar = trim($this->aadhaar);

        if (!AadhaarHelper::validate($this->aadhaar)) {
            $this->error = "Invalid Aadhaar number";
            $this->dispatch('hideLoader');
            return ['status' => 'error', 'message' => $this->error];
        }

        $encoded_aadhar = Crypt::encryptString($this->aadhaar);
        $aadhaar_hash = md5($this->aadhaar);

        $exists = BeneficiaryAadhaar::where('aadhar_hash', $aadhaar_hash)
            ->where('scheme_id', $this->schemeId)
            ->exists();

        if ($exists) {
            $this->error = "Duplicate Aadhaar found for this scheme!";
            $this->dispatch('hideLoader');
            return ['status' => 'duplicate', 'message' => $this->error];
        }

        $this->dispatch('aadhaarChecked', [
            'encoded' => $encoded_aadhar,
            'hash' => $aadhaar_hash
        ]);

        $this->dispatch('hideLoader');

        return ['status' => 'success', 'message' => '✅ Aadhaar is valid and not duplicate.'];
    }
    public function render()
    {
        return view('livewire.dup-aadhaar-check');
    }
}
