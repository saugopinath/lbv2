<?php

namespace App\Livewire;

use Livewire\Component;

class RevertRejectModal extends Component
{
    public $open = false;
    public $remark = '';
    public $action = '';
    public $revertrejectCauses = [];
    public $cause = '';

    protected $listeners = [
        'open-bulk-revert-modal' => 'openModal'
    ];

    public function openModal($action, $revertrejectCauses = [])
    {
        $this->reset(['remark', 'cause']);
        $this->open = true;
        $this->action = $action;
        $this->revertrejectCauses = $revertrejectCauses ?? [];
    }

    public function close()
    {
        $this->reset(['open', 'remark', 'cause']);
    }

    public function confirm()
    {
        $rules = [
            'remark' => 'required|string|max:500',
        ];

        // Cause only required for revert & reject
        if (in_array($this->action, ['revert', 'reject'])) {
            $rules['cause'] = 'required';
        }

        $validated = $this->validate($rules);

        $this->dispatch('confirm-bulk-revert', $validated);

        $this->close();
    }

    public function render()
    {
        return view('livewire.revert-reject-modal');
    }
}
