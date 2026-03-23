<?php
// app/Livewire/DynamicSidebar.php

namespace App\Livewire;

use Livewire\Component;
use App\Helpers\MenuHelper;

class DynamicSidebar extends Component
{
    public $menus = [];
    public $expandedMenus = [];
    
    protected $listeners = ['refreshSidebar' => 'loadMenus'];
    
    public function mount()
    {
        $this->loadMenus();
    }
    
    public function loadMenus()
    {
        $this->menus = MenuHelper::getMenus();
    }
    
    public function toggleMenu($menuId)
    {
        if (in_array($menuId, $this->expandedMenus)) {
            $this->expandedMenus = array_diff($this->expandedMenus, [$menuId]);
        } else {
            $this->expandedMenus[] = $menuId;
        }
    }
    
    public function isMenuExpanded($menuId)
    {
        return in_array($menuId, $this->expandedMenus);
    }
    
    public function render()
    {
        return view('livewire.dynamic-sidebar');
    }
}