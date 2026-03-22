<?php
// app/Livewire/DynamicSidebar.php
namespace App\Livewire;

use App\Services\MenuService;
use Livewire\Component;

class DynamicSidebar extends Component
{
    public $sidebar = true;
    public $activeMenu = null;
    public $menus = [];
    
    protected $menuService;
    
    public function boot(MenuService $menuService)
    {
        $this->menuService = $menuService;
    }
    
    public function mount()
    {
        $this->loadMenus();
    }
    
    public function loadMenus()
    {
        $this->menus = $this->menuService->getUserMenus();
    }
    
    public function toggleSidebar()
    {
        $this->sidebar = !$this->sidebar;
        $this->activeMenu = null;
    }
    
    public function setActiveMenu($menuId)
    {
        $this->activeMenu = $this->activeMenu === $menuId ? null : $menuId;
    }
    
    public function render()
    {
        return view('livewire.dynamic-sidebar');
    }
}