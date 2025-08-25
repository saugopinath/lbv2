<?php

namespace App\Livewire;
use App\Models\Role;
use Livewire\Component;

class RoleMappingLevel extends Component
{
 public $roles = [];
    public function mount()
    {
         $this->roles = Role::all();
    }    
    public function updatedselectedMappingLevel()
    {

    }

    public function updatedselectedselectedRole()
    {
        
    }
    public function render()
    {
        return view('livewire.role-mapping-level');
    }
}
