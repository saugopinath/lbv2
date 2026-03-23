<?php
// app/Livewire/MenuManagement.php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Menu;
use App\Models\Permission;
use App\Helpers\MenuHelper;
use Illuminate\Support\Facades\DB;

class MenuManagement extends Component
{
    public $menus = [];
    public $permissions = [];
    public $selectedMenu = null;
    public $isEditing = false;
    public $search = '';
    public $showModal = false;
    public $showDeleteModal = false;
    public $menuToDelete = null;
    
    // Form fields
    public $menu_name = '';
    public $icon = '';
    public $route = '';
    public $url = '';
    public $parent_id = '';
    public $menu_rank = 0;
    public $is_active = true;
    public $selectedPermissions = [];
    
    protected $rules = [
        'menu_name' => 'required|string|max:255',
        'icon' => 'nullable|string|max:100',
        'route' => 'nullable|string|max:255',
        'url' => 'nullable|string|max:255',
        'parent_id' => 'nullable|exists:menus,id',
        'menu_rank' => 'required|integer',
        'is_active' => 'boolean',
        'selectedPermissions' => 'array',
    ];
    
    public function mount()
    {
        $this->loadMenus();
        $this->loadPermissions();
    }
    
    public function loadMenus()
    {
        $this->menus = MenuHelper::getMenuTree();
    }
    
    public function loadPermissions()
    {
        // Get all permissions
        $this->permissions = Permission::orderBy('name')->get();
    }
    
    public function editMenu($menuId)
    {
        $this->selectedMenu = Menu::find($menuId);
        $this->menu_name = $this->selectedMenu->menu_name;
        $this->icon = $this->selectedMenu->icon;
        $this->route = $this->selectedMenu->route;
        $this->url = $this->selectedMenu->url;
        $this->parent_id = $this->selectedMenu->parent_id;
        $this->menu_rank = $this->selectedMenu->menu_rank;
        $this->is_active = $this->selectedMenu->is_active;
        $this->selectedPermissions = $this->selectedMenu->permission_id ?? [];
        $this->isEditing = true;
        $this->showModal = true;
        
        $this->dispatch('open-modal');
    }
    
    public function createMenu()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->showModal = true;
        
        $this->dispatch('open-modal');
    }
    
    public function saveMenu()
    {
        $this->validate();
        
        DB::beginTransaction();
        try {
            $data = [
                'menu_name' => $this->menu_name,
                'icon' => $this->icon,
                'route' => $this->route,
                'url' => $this->url,
                'parent_id' => $this->parent_id ?: null,
                'menu_rank' => $this->menu_rank,
                'is_active' => $this->is_active,
                'permission_id' => $this->selectedPermissions,
            ];
            
            if ($this->isEditing && $this->selectedMenu) {
                $this->selectedMenu->update($data);
                $message = 'Menu updated successfully';
            } else {
                Menu::create($data);
                $message = 'Menu created successfully';
            }
            
            DB::commit();
            
            $this->loadMenus();
            MenuHelper::clearCache();
            $this->resetForm();
            $this->showModal = false;
            
            $this->dispatch('close-modal');
            $this->dispatch('show-message', ['message' => $message, 'type' => 'success']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('show-message', ['message' => 'Error: ' . $e->getMessage(), 'type' => 'error']);
        }
    }
    
    public function confirmDelete($menuId)
    {
        $this->menuToDelete = Menu::find($menuId);
        
        if ($this->menuToDelete->children()->count() > 0) {
            $this->dispatch('show-message', [
                'message' => 'Cannot delete menu with children. Delete child menus first.',
                'type' => 'error'
            ]);
            return;
        }
        
        $this->showDeleteModal = true;
        $this->dispatch('open-delete-modal');
    }
    
    public function deleteMenu()
    {
        if ($this->menuToDelete) {
            $this->menuToDelete->delete();
            $this->loadMenus();
            MenuHelper::clearCache();
            $this->showDeleteModal = false;
            $this->dispatch('show-message', ['message' => 'Menu deleted successfully', 'type' => 'success']);
        }
    }
    
    public function toggleStatus($menuId)
    {
        $menu = Menu::find($menuId);
        $menu->is_active = !$menu->is_active;
        $menu->save();
        
        $this->loadMenus();
        MenuHelper::clearCache();
        
        $this->dispatch('show-message', [
            'message' => 'Menu ' . ($menu->is_active ? 'activated' : 'deactivated'),
            'type' => 'success'
        ]);
    }
    
    private function resetForm()
    {
        $this->menu_name = '';
        $this->icon = '';
        $this->route = '';
        $this->url = '';
        $this->parent_id = '';
        $this->menu_rank = 0;
        $this->is_active = true;
        $this->selectedPermissions = [];
        $this->selectedMenu = null;
    }
    
    public function render()
    {
        $parentMenus = Menu::whereNull('parent_id')
            ->orderBy('menu_rank')
            ->with('children')
            ->get();
        
        return view('livewire.menu-management', [
            'parentMenus' => $parentMenus,
         ])->layout('components.layouts.app');
    }
}