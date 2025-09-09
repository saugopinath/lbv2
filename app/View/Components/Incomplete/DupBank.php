<?php

namespace App\View\Components\Incomplete;

use App\Models\Ifsccodemaster;
use Illuminate\View\Component;

class DupBank extends Component
{
    public $item;
    public $formData;
    public $ifscode;
    public $bankname;
    public $bankbranchname;

    public function __construct($item, $formData = [], $ifscode = null)
    {
        $this->item = $item;
        $this->formData = $formData;
        $this->ifscode = $ifscode;

        if ($this->ifscode) {
            // DD()
            $ifs = Ifsccodemaster::with('bank')
                ->where('code', $this->ifscode)
                ->where('is_active', 1)
                ->first();

            if ($ifs) {
                $this->bankname       = $ifs->bank->name ?? '';

                $this->bankbranchname = $ifs->branch ?? '';
            } else {
                $this->bankname       = '';
                $this->bankbranchname = '';
            }
        }
    }

    public function render()
    {
        return view('components.incomplete.dup-bank');
    }
}
