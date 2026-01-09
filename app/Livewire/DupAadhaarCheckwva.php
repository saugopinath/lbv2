<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\BeneficiaryAadhaar;
use App\Models\BeneficiaryCommonList;
use Illuminate\Support\Facades\Crypt;

class DupAadhaarCheck extends Component
{
    public $aadhaar;
    public $error = null;

    public function checkDuplicate()
    {
        $this->dispatch('showLoader');
        // dump('ok');
        $this->error = null;
        $this->aadhaar = trim($this->aadhaar);
        if (!ctype_digit($this->aadhaar) || strlen($this->aadhaar) !== 12) {
            $this->dispatch('hideEntryTabs');
            $this->dispatch('hideLoader');
            $this->error = "Please enter a valid 12-digit Aadhaar number.";
            return ['status' => 'error', 'message' => $this->error];
        }

        $encoded_aadhar = Crypt::encryptString($this->aadhaar);
        $aadhaar_hash = md5($this->aadhaar);
        if (BeneficiaryAadhaar::where('aadhar_hash', $aadhaar_hash)->exists()) {
            $this->dispatch('hideEntryTabs');
            $this->dispatch('hideLoader');
            $this->error = "Duplicate Aadhaar found!";
            return ['status' => 'duplicate', 'message' => $this->error];
        }
        // dump('ok1');
        $this->dispatch('aadhaarChecked', [
            'encoded' => $encoded_aadhar,
            'hash' => $aadhaar_hash,
        ]);
//         dump('ok2');
// sleep(5);
// dump('ok3');
        $this->dispatch('hideLoader');
        // dump('ok4');
        return ['status' => 'success', 'message' => '✅ Aadhaar is valid and not duplicate.'];
    }
    public function getDuplicates()
    {
        $aadhar = $this->aadhaar;
        $records = BeneficiaryCommonList::all();
        $duplicates = $records->filter(function ($record) use ($aadhar) {
            try {
                return Crypt::decryptString($record->encoded_aadhar) === $aadhar;
            } catch (\Exception $e) {
                return false;
            }
        });
        if ($duplicates->isNotEmpty()) {
            return $duplicates;
        }
        return collect();
    }
    public function render()
    {
        return view('livewire.dup-aadhaar-check');
    }
}
