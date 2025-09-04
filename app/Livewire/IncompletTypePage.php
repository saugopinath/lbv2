<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ApplicantIncompletDeatil;

class IncompletTypePage extends Component
{
    public $id;
    public $page;

    public function mount($id)
    {
        $this->id = $id;

        $this->page = ApplicantIncompletDeatil::where('application_id', $id)
            ->with('incompletType')
            ->get();
    }

    public function submit()
    {
        dd('ok');
    }

    public function render()
    {
        return view('livewire.incomplet-type-page', [
            'page' => $this->page,
        ]);
    }
}
