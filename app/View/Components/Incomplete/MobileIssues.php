<?php

namespace App\View\Components\Incomplete;

use Illuminate\View\Component;

class MobileIssues extends Component
{
    public $mobileIssues, $stage;

    public function __construct($mobileIssues, $stage = null)
    {
        $this->mobileIssues = $mobileIssues;
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
    
        return view('components.incomplete.mobile-issues',compact('stage'));
    }
}
