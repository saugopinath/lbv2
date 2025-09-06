<?php

namespace App\View\Components\Incomplete;

use Illuminate\View\Component;

class MismatchHigh extends Component
{
    public $item;

    public function __construct($item)
    {
        $this->item = $item;
    }

    public function render()
    {
        return view('components.incomplete.mismatch-high');
    }
}
