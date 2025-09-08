<?php

namespace App\View\Components\Incomplete;

use Illuminate\View\Component;

class BankIssues extends Component
{
    /**
     * The bank issues list.
     *
     * @var array
     */
    public $bankIssues;

    /**
     * Create a new component instance.
     *
     * @param array $bankIssues
     */
    public function __construct($bankIssues)
    {
        $this->bankIssues = $bankIssues;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.incomplete.bank-issues');
    }
}
