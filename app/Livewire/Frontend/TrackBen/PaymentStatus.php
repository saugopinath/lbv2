<?php

namespace App\Livewire\Frontend\TrackBen;

use Livewire\Component;

class PaymentStatus extends Component
{
    public $ben_id;
    public $scheme_id;
    public $ben_status;
    public $bank_code;
    public $ifsc;

    public $fin_year = '2026-2027'; // default

    public function render()
    {
        $currentYear = date('Y');
        $currentMonth = date('m');
        if ($currentMonth > 3) {
            $start = $currentYear;
        } else {
            $start = $currentYear - 1;
        }

        $years = [];
        for ($i = 0; $i < 3; $i++) {
            $y = $start - $i;
            $years[] = $y . '-' . ($y + 1);
        }

        return view('livewire.frontend.track-ben.payment-status', [
            'available_years' => $years
        ]);
    }
}
