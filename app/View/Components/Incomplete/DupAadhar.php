<?php
namespace App\View\Components\Incomplete;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Crypt;

class DupAadhar extends Component
{
    public $item;
    public $stage;
    public $formData;
    public $aadhaarDecrypted;

    public function __construct($item, $stage = null, $formData = [])
    {
        $this->item = $item;
        $this->stage = $stage;
        $this->formData = $formData;

        $aadhaar = $formData['new_aadhar'][$item->id] ?? ($item->new_value ?? null);

        $this->aadhaarDecrypted = Crypt::decryptString($aadhaar);
        
    }

    public function render()
    {
        return view('components.incomplete.dup-aadhar');
    }
}