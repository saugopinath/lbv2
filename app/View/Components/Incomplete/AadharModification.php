<?php

namespace App\View\Components\Incomplete;

use Illuminate\View\Component;

class AadharModification extends Component
{
    public $aadhaarIssues;

    /**
     * Create a new component instance.
     */
    public function __construct($aadhaarIssues = [])
    {
        $this->aadhaarIssues = $aadhaarIssues;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.incomplete.aadhar-modification');
    }
}
