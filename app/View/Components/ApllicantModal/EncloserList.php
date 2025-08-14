<?php

namespace App\View\Components\apllicantModal;

use Closure;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use App\Models\BeneficiaryEnclosure;
use Illuminate\Support\Facades\Crypt;

class EncloserList extends Component
{
    /**
     * Create a new component instance.
     */
    public $id, $applicantDet, $decryptedEncloser;

    public function __construct($id)
    {
        $applicantDet = BeneficiaryEnclosure::with('documents')->where('application_id', $id)->get();
        dd( $applicantDet);

    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.apllicant-modal.encloser-list');
    }
}
