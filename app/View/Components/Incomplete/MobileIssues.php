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
        return view('components.incomplete.mobile-issues');
    }
}
