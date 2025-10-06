<?php

namespace App\View\Components\Incomplete;

use Illuminate\View\Component;

class MobileIssues extends Component
{
    public $mobileIssues;

    public function __construct($mobileIssues)
    {
        $this->mobileIssues = $mobileIssues;
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
