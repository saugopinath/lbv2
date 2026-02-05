<?php

namespace App\Livewire;

use Livewire\Component;

class FinalSubmitModal extends Component
{
    public $show = false;
    public $applicationId;
    public array $tabsData = [];
    public $previewTabCode = null;
    public $schemeId;


    // 🔥 LISTENER
    protected $listeners = ['openFinalModal'];

    /* ================= RECEIVE DATA ================= */

    public function openFinalModal($applicationId, $tabsData, $schemeId = null)
    {
        $this->applicationId = $applicationId;
        $this->tabsData = $tabsData;
        $this->schemeId = $schemeId;
        $this->show = true;
    }

    /* ================= ACTIONS ================= */

    public function close()
    {
        $this->show = false;
    }

    public function confirmSubmit()
    {
        // এখানে final submit logic বসাবে (DB update etc.)

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
