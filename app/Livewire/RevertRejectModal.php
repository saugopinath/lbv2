<?php

namespace App\Livewire;

use Livewire\Component;

class RevertRejectModal extends Component
{
    public $open = false;
    public $remark = '';
    public $action = '';
    public $revertrejectCauses = '';
    public $cause = '';
    protected $listeners = ['open-bulk-revert-modal' => 'openModal'];

    public function openModal($action, $revertrejectCauses)
    {
        $this->open = true;
        $this->action = $action;
        $this->revertrejectCauses = $revertrejectCauses;
    }

    public function close()
    {
        $this->open = false;
        $this->remark = '';
        $this->cause = '';
    }

    public function confirm()
    {
        $validated = $this->validate([
            'remark' => 'required|string|max:500',
            'cause' => 'required',
        ]);
        $this->dispatch('confirm-bulk-revert',$validated);
        $this->close();
    }

    public function render()
    {
        return view('livewire.revert-reject-modal');
    }
}
