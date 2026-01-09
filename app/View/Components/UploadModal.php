<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class UploadModal extends Component
{
    /**
     * Create a new component instance.
     */
    public $currentDocExtensions;
    public $currentDocMaxSize;

    public function __construct($currentDocExtensions = '', $currentDocMaxSize = '')
    {
        $this->currentDocExtensions = $currentDocExtensions;
        $this->currentDocMaxSize = $currentDocMaxSize;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.upload-modal');
    }
}
