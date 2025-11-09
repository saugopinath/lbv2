<?php

namespace App\Livewire;

use Livewire\Component;

class UserManagementContainer extends Component
{
    public $showAddUserForm = false;

    protected $listeners = ['userAdded' => 'handleUserAdded'];

    public function toggleForm()
    {
        $this->showAddUserForm = !$this->showAddUserForm;
    }

    public function handleUserAdded()
    {
        $this->showAddUserForm = false;
        $this->emit('refreshUserTable'); 
    }

    public function render()
    {
        return view('livewire.user-management-container');
    }
}
