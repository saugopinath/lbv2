<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Session;
use App\Models\BeneficiaryAadhaar;
use Illuminate\Support\Facades\Crypt;
use App\Helpers\AadhaarHelper;

class DupAadhaarCheck extends Component
{
    public $aadhaar;
    public $error = null;
    public $valid = false;

    public function checkDuplicate()
    {
        $this->error = null;
        $this->valid = false;
        $this->aadhaar = trim($this->aadhaar);

        // Verhoeff validation via helper
        if (!AadhaarHelper::validate($this->aadhaar)) {
            $this->error = "Invalid Aadhaar number sc";
            return ['status' => 'error', 'message' => $this->error];
        }

        $encoded_aadhar = Crypt::encryptString($this->aadhaar);
        $aadhaar_hash = md5($this->aadhaar);

        // Check duplicate
        if (BeneficiaryAadhaar::where('aadhar_hash', $aadhaar_hash)->exists()) {
            $this->error = "Duplicate Aadhaar found!";
            return ['status' => 'duplicate', 'message' => $this->error];
        }

        // Aadhaar is valid
        $this->valid = true;
        Session::put('aadhaar_data', [
            'encoded' => $encoded_aadhar,
            'hash' => $aadhaar_hash,
        ]);

        $this->dispatch('aadhaarChecked');

        return ['status' => 'success', 'message' => '✅ Aadhaar is valid and not duplicate.'];
    }

    public function render()
    {
        return view('livewire.dup-aadhaar-check');
    }
}
