<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Ifsccodemaster;

class BankDetails extends Component
{
    public $mode;
    public $ifscode;
    public $bankname;
    public $bankbranchname;

    public function updatedIfscode() // correct method name for snake_case property
    {
        $ifs = Ifsccodemaster::with('bank')
            ->where('code', $this->ifscode)
            ->where('is_active', 1)
            ->first();

        if ($ifs && $ifs->bank && $ifs->bank->is_active) {
            $this->bankname = $ifs->bank->name;
            $this->bankbranchname = $ifs->branch;
        } else {
            $this->bankname = '';
            $this->bankbranchname = '';
        }
    }
    public function mount($mode = null)
    {
        // $ifs = Ifsccodemaster::with('bank')->where('code', 'SBIN0009136')->where('is_active', 1)->first();
        // dd($ifs);
        $this->mode = $mode;
    }
    public function render()
    {
        return view('livewire.bank-details');
    }
}
