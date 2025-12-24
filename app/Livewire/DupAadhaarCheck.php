<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\BeneficiaryAadhaar;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;
use App\Helpers\AadhaarHelper;
use App\Helpers\WorkFlowPermissionHelper;

class DupAadhaarCheck extends Component
{
    public $aadhaar, $grievanceId;
    public $error = null;
    public function mount()
    {
        if (request()->has('id')) {
            $this->grievanceId = request()->query('id');
        }
    }

    public function checkDuplicate($previousAadhaar = null)
    {
        $this->error = null;
        $this->aadhaar = trim($this->aadhaar);
        if (!AadhaarHelper::validate($this->aadhaar)) {
            $this->error = "Invalid Aadhaar number sc";
            $this->dispatch('hideLoader');
            return ['status' => 'error', 'message' => $this->error];
        }
        $encoded_aadhar = Crypt::encryptString($this->aadhaar);
        $aadhaar_hash = md5($this->aadhaar);
        if (BeneficiaryAadhaar::where('aadhar_hash', $aadhaar_hash)->exists()) {
            $this->error = "Duplicate Aadhaar found!";
            $this->dispatch('hideLoader');
            return ['status' => 'duplicate', 'message' => $this->error];
        }

        // $user = Auth::user();
        // if (!$user->can('Normal Entry Allow') && !$user->can('Duare Sarkar Entry Allow')) {
        //     $this->error = "Not authorized to create entry.";
        //     $this->dispatch('hideLoader');
        //     return ['status' => 'unauthorized', 'message' => $this->error];
        // }

        if (!WorkFlowPermissionHelper::canCreateEntry()) {
            $this->error = "Not authorized to create entry.";
            $this->dispatch('hideLoader');
            return ['status' => 'unauthorized', 'message' => $this->error];
        }

        $this->dispatch('aadhaarChecked', [
            'encoded' => $encoded_aadhar,
            'hash' => $aadhaar_hash,
            'grievance_id' => $this->grievanceId,
        ]);
        $this->dispatch('hideLoader');
        return ['status' => 'success', 'message' => '✅ Aadhaar is valid and not duplicate.'];
    }
    public function render()
    {
        return view('livewire.dup-aadhaar-check');
    }
}
