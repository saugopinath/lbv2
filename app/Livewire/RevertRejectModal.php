<?php

namespace App\Livewire;

use Livewire\Component;

class RevertRejectModal extends Component
{
    public $open = false;
    public $reason = '';
    public $action = '';

    protected $listeners = ['open-bulk-revert-modal' => 'openModal'];

    public function openModal($action)
    {
        $this->open = true;
        $this->action = $action;
    }

    public function close()
    {
        $this->open = false;
        $this->reason = '';
    }

    public function confirm()
    {
        $this->validate([
            'reason' => 'required|string|max:500',
        ]);
        $this->dispatch('confirm-bulk-revert', [
            'reason' => $this->reason,
        ]);
        $this->close();
    }

    public function render()
    {
        return view('livewire.revert-reject-modal');
    }
}
