<?php

namespace App\Livewire;

use Livewire\Component;

class Notification extends Component
{
    public $message = '';
    public $type = 'success';
    public $show = false;

    protected $listeners = ['notify'];

    // Trigger notification
    public function notify($message, $type = 'success', $timeout = 3000)
    {
        $this->message = $message;
        $this->type = $type;
        $this->show = true;

        // Auto-hide
        $this->dispatch('hide-notification', ['timeout' => $timeout]);
    }

    // Hide notification
    public function hide()
    {
        $this->show = false;
        $this->message = '';
    }

    public function render()
    {
        return view('livewire.notification');
    }
}
