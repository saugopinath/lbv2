<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class AccordionSection extends Component
{
    public $title,$sectionId,$color;

    public function __construct($title, $sectionId, $color)
    {
        $this->title = $title;
        $this->sectionId = $sectionId;
        $this->color = $color;
    }

    public function render(): View
    {
        return view('components.accordion-section');
    }
}
