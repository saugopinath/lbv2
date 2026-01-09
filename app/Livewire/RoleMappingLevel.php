<?php

namespace App\Livewire;
use App\Models\Role;
use Livewire\Component;
use App\Models\Codemaster;

class RoleMappingLevel extends Component
{
 public $roles = [];
 public $mapping_levels = [];
    public function mount()
    {
         $this->roles = Role::all();
        $this->mapping_levels = Codemaster::where('parent_id', 15)->get();
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
