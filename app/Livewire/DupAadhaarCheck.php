<?php
namespace App\Livewire;
use Livewire\Component;
use Illuminate\Support\Facades\Session;
use App\Models\BeneficiaryAadhaar;
use Illuminate\Support\Facades\Crypt;
class DupAadhaarCheck extends Component
{
    public $aadhaar;
    public $error = null;
    public function checkDuplicate()
    {
        $this->error = null;
        $this->aadhaar = trim($this->aadhaar);
        if (!ctype_digit($this->aadhaar) || strlen($this->aadhaar) !== 12) {
            $this->error = "Please enter a valid 12-digit Aadhaar number.";
            return ['status' => 'error', 'message' => $this->error];
        }
        $encoded_aadhar = Crypt::encryptString($this->aadhaar);
        $aadhaar_hash = md5($this->aadhaar);
        if (BeneficiaryAadhaar::where('aadhar_hash', $aadhaar_hash)->exists()) {
            $this->error = "Duplicate Aadhaar found!";
            return ['status' => 'duplicate', 'message' => $this->error];
        }
        $this->dispatch('aadhaarChecked', [
            'encoded' => $encoded_aadhar,
            'hash' => $aadhaar_hash,
        ]);
        return ['status' => 'success', 'message' => '✅ Aadhaar is valid and not duplicate.'];
    }
    public function render()
    {
        return view('livewire.dup-aadhaar-check');
    }
}