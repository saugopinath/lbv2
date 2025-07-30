<?php
namespace App\Livewire;
use Livewire\Component;
use Illuminate\Support\Facades\Session;
use App\Models\BeneficiaryAadhaar;
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
        if (!ctype_digit($this->aadhaar) || strlen($this->aadhaar) !== 12) {
            $this->error = "Please enter a valid 12-digit Aadhaar number.";
            return;
        }
        $hash = hash('sha256', $this->aadhaar);
        if (BeneficiaryAadhaar::where('aadhar_hash', $hash)->exists()) {
            $this->error = "Duplicate Aadhaar found!";
            // session()->forget('aadhaar_valid');
            return;
        }

        $this->valid = true;
        // session()->put('aadhaar_valid', true);
        Session::put('aadhar_hash', $hash);
        $this->dispatch('aadhaarChecked');
    }
    public function render()
    {
        return view('livewire.dup-aadhaar-check');
    }
}