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
        // dd($this->schemeId);
        $this->error = null;
        $this->aadhaar = trim($this->aadhaar);

        // Commented for Dev
        // if (!AadhaarHelper::validate($this->aadhaar)) {
        //     $this->error = "Invalid Aadhaar number";
        //     $this->dispatch('hideLoader');
        //     return ['status' => 'error', 'message' => $this->error];
        // }

        $encoded_aadhar = Crypt::encryptString($this->aadhaar);
        $aadhaar_hash = md5($this->aadhaar);

        $exists = BeneficiaryAadhaar::where('aadhaar_hash', $aadhaar_hash)
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
            'encoded' => $encoded_aadhar,
            'hash' => $aadhaar_hash
        ]);

        $this->dispatch('hideLoader');

        return ['status' => 'success', 'message' => '✅ Aadhaar is valid and not duplicate.'];
    }
    public function DsMark()
    {
        // Session::put('dup_aadhaar', md5(trim($this->aadhaar)));
        $this->showDsTable = true;
        $this->dispatch('aadhaarCheckedds', [
            'aadhar_hash' => md5(trim($this->aadhaar))
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
