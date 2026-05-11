<?php

namespace App\View\Components\Incomplete;

use Illuminate\View\Component;

class AadharModification extends Component
{
    public $aadhaarIssues, $schemeId, $stage;

    public function __construct($aadhaarIssues = [], $schemeId = null, $stage = null)
    {
        $this->aadhaarIssues = $aadhaarIssues;
        $this->schemeId = $schemeId;
        $this->stage = $stage;
    }

    public function render()
    {
        $user = auth()->user();

        $stage = $this->stage ?? null;


        if (!$stage) {
            if ($user->hasAnyRole(['Verifier', 'Delegated Verifier'])) {
                $stage = 'verifier';

            } elseif ($user->hasAnyRole(['Approver', 'Delegated Approver'])) {
                $stage = 'approver';
            }

        }        
        return view('components.incomplete.aadhar-modification', compact('stage'));

    }
}
