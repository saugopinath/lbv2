<?php

namespace App\View\Components\Incomplete;

use Illuminate\View\Component;

class AadharModification extends Component
{
    public $aadhaarIssues;

    public function __construct($aadhaarIssues = [])
    {
        $this->aadhaarIssues = $aadhaarIssues;
    }

    public function render()
    {
        return view('components.incomplete.aadhar-modification');
    }
}
