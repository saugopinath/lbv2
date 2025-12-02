<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\BeneficiaryAadhaar;
use Illuminate\Support\Facades\Crypt;
use App\Helpers\AadhaarHelper;
use App\Helpers\WorkFlowPermissionHelper;
use Illuminate\Support\Facades\Session;

class DupAadhaarCheck extends Component
{
    public $aadhaar, $grievanceId;
    public function mount()
    {
        if (request()->has('id')) {
            $this->grievanceId = request()->query('id');
        }
    }
    public function checkDuplicate()
    {
        if (!WorkFlowPermissionHelper::canCreateEntry()) {
            $this->dispatch('hideLoader');
            return [
                'status' => 'unauthorized',
                'message' => "Not authorized to create entry."
            ];
        }
        $this->aadhaar = trim($this->aadhaar);
        if (!AadhaarHelper::validate($this->aadhaar)) {
            $this->dispatch('hideLoader');
            return [
                'status' => 'error',
                'message' => "Invalid Aadhaar number sc"
            ];
        }
        $encoded_aadhar = Crypt::encryptString($this->aadhaar);
        $aadhaar_hash = md5($this->aadhaar);
        if (BeneficiaryAadhaar::where('aadhar_hash', $aadhaar_hash)->exists()) {
            $this->dispatch('hideLoader');
            return [
                'status' => 'duplicate',
                'message' => "Duplicate Aadhaar found!",
                'ds_entry' => WorkFlowPermissionHelper::canDuareSarkarEntryAllow()
            ];
        }
        $this->dispatch('aadhaarChecked', [
            'encoded' => $encoded_aadhar,
            'hash' => $aadhaar_hash,
            'grievance_id' => $this->grievanceId,
        ]);
        $this->dispatch('hideLoader');
        return [
            'status' => 'success',
            'message' => "✅ Aadhaar is valid and not duplicate."
        ];
    }
    public function FindDuplicate()
    {
        // Session::put('dup_aadhaar', Crypt::encrypt(trim($this->aadhaar)));
        // Session::put('dup_bank', '123456');
        // dd(Session::get('dup_aadhaar'), Session::get('dup_bank'));
        dd('FindDuplicate');
    }
    public function DsMark()
    {
        Session::put('dup_aadhaar', Crypt::encrypt(trim($this->aadhaar)));
        // Session::put('dup_bank', '123456');
        // dd(Session::get('dup_aadhaar'), Session::get('dup_bank'));
        return [
            'status' => true
        ];
    }
    public function render()
    {
        return view('livewire.dup-aadhaar-check');
    }
}
