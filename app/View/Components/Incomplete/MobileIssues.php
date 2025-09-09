<?php

namespace App\View\Components\Incomplete;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class MobileIssues extends Component
{
    public $mobileIssues;

    public function __construct($mobileIssues)
    {
        $this->mobileIssues = $mobileIssues;
        // dd($this->mobileIssues);
    }

    public function render(): View|Closure|string
    {
        return view('components.incomplete.mobile-issues');
    }
}
