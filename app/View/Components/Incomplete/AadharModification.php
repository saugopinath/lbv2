<?php

namespace App\View\Components\Incomplete;

use Illuminate\View\Component;

class AadharModification extends Component
{
    public $aadhaarIssues,$schemeId;
    public $formData = [];

    public function __construct($aadhaarIssues = [], $schemeId = null)
    {
        $this->aadhaarIssues = $aadhaarIssues;
        $this->schemeId = $schemeId;  

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
        //  dd($stage);
        return view('components.incomplete.aadhar-modification', compact('stage'));

    }
}
