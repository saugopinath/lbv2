<?php

namespace App\View\Components\Incomplete;

use Illuminate\View\Component;

class AadharModification extends Component
{
    public $aadhaarIssues;
    public $formData = [];

    public function __construct($aadhaarIssues = [])
    {
        $this->aadhaarIssues = $aadhaarIssues;
        // dd($this->aadhaarIssues);
    }

    public function render()
    {
        return view('components.incomplete.aadhar-modification');
    }
}
