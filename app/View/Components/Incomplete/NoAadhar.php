<?php

namespace App\View\Components\Incomplete;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Crypt;

class NoAadhar extends Component
{
    public $item;
    public $stage;
    public $formData;

    public function __construct($item, $stage = null,$formData = [])
    {
        $this->item = $item;
        $this->stage = $stage;
        $this->formData = $formData;
        
    }

    public function render()
    {
        return view('components.incomplete.no-aadhar');
    }
}
