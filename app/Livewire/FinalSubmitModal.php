<?php

namespace App\Livewire;

use Livewire\Component;

class FinalSubmitModal extends Component
{
    public $show = false;
    public $applicationId;
    public $tabsData = [];

    protected $listeners = ['openFinalModal'];

    public function openFinalModal($applicationId, $tabsData)
    {
        $this->applicationId = $applicationId;
        $this->tabsData = $tabsData;
        $this->show = true;
    }

    public function close()
    {
        $this->show = false;
    }

    public function confirmSubmit()
    {
        // Example:
        // Application::find($this->applicationId)
        //     ->update(['status' => 'submitted']);

        $this->show = false;

        $this->dispatch(
            'notify',
            type: 'success',
            message: 'Application Final Submitted Successfully!'
        );
    }

    public function render()
    {
        return view('livewire.final-submit-modal');
    }
}
