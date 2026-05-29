<?php

namespace App\View\Components\apllicantModal;

use Closure;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use App\Models\BeneficiaryEnclosure;

class EncloserList extends Component
{
    public $id;
    public $decryptedEncloser;

    public function __construct($id)
    {
        $enclosures = BeneficiaryEnclosure::with('documents')
            ->where('beneficiary_id', $id)
            ->orWhere('application_id', $id)
            ->get();

        foreach ($enclosures as $enclosure) {
            if (!empty($enclosure->attched_document)) {
                if (\Illuminate\Support\Str::isUuid($enclosure->attched_document)) {
                    $enclosure->attched_document = route('document.view', $enclosure->id);
                } else {
                    $enclosure->attched_document = 'data:' . $enclosure->document_mime_type . ';base64,' . $enclosure->attched_document;
                }
            } else {
                $enclosure->attched_document = null;
            }
        }

        $this->decryptedEncloser = $enclosures;
    }

    // public function __construct($id)
    // {
    //     $reportType = request()->query('reportType');
    //     // dd($reportType);

    //     if ($reportType === '3') {
    //         $enclosures = BeneficiaryEnclosure::with('documents')
    //             ->where('beneficiary_id', $id)
    //             ->get();
    //     } else {
    //         $enclosures = BeneficiaryEnclosure::with('documents')
    //             ->where('application_id', $id)
    //             ->get();
    //     }

    //     foreach ($enclosures as $enclosure) {
    //         if (!empty($enclosure->attched_document)) {
    //             $enclosure->attched_document = 'data:' . $enclosure->document_mime_type . ';base64,' . $enclosure->attched_document;
    //         } else {
    //             $enclosure->attched_document = null;
    //         }
    //     }

    //     $this->decryptedEncloser = $enclosures;
    // }

    public function render(): View|Closure|string
    {
        return view('components.apllicant-modal.encloser-list', [
            'decryptedEncloser' => $this->decryptedEncloser
        ]);
    }
}
