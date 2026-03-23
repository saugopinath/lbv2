<?php
// app/Livewire/MenuManagement.php
namespace App\Livewire;

use App\Models\Menu;
use App\Models\Role;
use App\Services\MenuService;
use Livewire\Component;
use Livewire\WithPagination;

class MenuManagement extends Component
{
    use WithPagination;
    
    public $menuId;
    public $name;
    public $icon;
    public $route;
    public $url;
    public $parent_id;
    public $order;
    public $is_active = true;
    public $permission_key;
    public $selectedRoles = [];
    public $scheme_id;
    public $department_id;
    public $showForm = false;
    public $isEditing = false;
    public $showJson = false;
    public $generatedJson = '';
    
    protected $rules = [
        'name' => 'required|min:2|max:100',
        'icon' => 'nullable|max:50',
        'route' => 'nullable|max:100',
        'url' => 'nullable|max:200',
        'parent_id' => 'nullable|exists:menus,id',
        'order' => 'nullable|integer',
        'is_active' => 'boolean',
        'permission_key' => 'nullable|max:100',
        'selectedRoles' => 'array',
        'scheme_id' => 'required|exists:schemes,id',
        'department_id' => 'required|exists:departments,id'
    ];
    
    protected $menuService;
    
    public function boot(MenuService $menuService)
    {
        $this->menuService = $menuService;
    }
    
    public function render()
    {
        // For sidebar and JSON mode, show from JSON files (not DB directly)
        $menusJson = $this->menuService->getUserMenus();

        // Keep DB listing for editing management
        $menusDb = Menu::with('children', 'roles', 'scheme', 'department')
            ->whereNull('parent_id')
            ->orderBy('order')
            ->paginate(20);

        $roles = Role::orderBy('name')->get();
        $parentMenus = Menu::orderBy('order')->get();
        $schemes = \App\Models\Scheme::orderBy('name')->get();
        $departments = \App\Models\Department::orderBy('name')->get();

        return view('livewire.menu-management', [
            'menusDb' => $menusDb,
            'menusJson' => $menusJson,
            'roles' => $roles,
            'parentMenus' => $parentMenus,
            'schemes' => $schemes,
            'departments' => $departments
        ]);
    }
    
    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
        $this->isEditing = false;
    }
    
    public function edit($id)
    {
        $menu = Menu::with('roles')->findOrFail($id);
        
        $this->menuId = $menu->id;
        $this->name = $menu->name;
        $this->icon = $menu->icon;
        $this->route = $menu->route;
        $this->url = $menu->url;
        $this->parent_id = $menu->parent_id;
        $this->order = $menu->order;
        $this->is_active = $menu->is_active;
        $this->permission_key = $menu->permission_key;
        $this->selectedRoles = $menu->roles->pluck('id')->toArray();
        $this->scheme_id = $menu->scheme_id;
        $this->department_id = $menu->department_id;
        
        $this->showForm = true;
        $this->isEditing = true;
    }
    
    private function normalizeIcon($icon)
    {
        $icon = trim($icon);
        if (empty($icon)) {
            return null;
        }

        // Normalize legacy Font Awesome helpers.
        $icon = preg_replace('/\bfa\s+(?=fa-)/', 'fas ', $icon);
        $icon = str_replace(
            ['fa-solid', 'fa-regular', 'fa-light', 'fa-duotone', 'fa-brands', 'fa'],
            ['fas', 'far', 'fal', 'fad', 'fab', 'fas'],
            $icon
        );
        $icon = preg_replace('/\s+/', ' ', $icon);

        return trim($icon);
    }

    public function save()
    {
        $this->validate();

        $icon = $this->normalizeIcon($this->icon);

        if ($this->isEditing) {
            $menu = Menu::findOrFail($this->menuId);
            $menu->update([
                'name' => $this->name,
                'icon' => $icon,
                'route' => $this->route,
                'url' => $this->url,
                'parent_id' => $this->parent_id,
                'order' => $this->order ?? 0,
                'is_active' => $this->is_active,
                'permission_key' => $this->permission_key,
                'scheme_id' => $this->scheme_id,
                'department_id' => $this->department_id
            ]);
            
            $message = 'Menu updated successfully!';
        } else {
            $menu = Menu::create([
                'name' => $this->name,
                'icon' => $icon,
                'route' => $this->route,
                'url' => $this->url,
                'parent_id' => $this->parent_id,
                'order' => $this->order ?? 0,
                'is_active' => $this->is_active,
                'permission_key' => $this->permission_key,
                'scheme_id' => $this->scheme_id,
                'department_id' => $this->department_id
            ]);
            
            $message = 'Menu created successfully!';
        }
        
        // Sync roles
        if (!empty($this->selectedRoles)) {
            $menu->roles()->sync($this->selectedRoles);
        }
        
        // Generate JSON for all roles
        $this->menuService->clearMenuCache();
        
        session()->flash('message', $message);
        
        $this->resetForm();
        $this->showForm = false;
    }
    
    public function delete($id)
    {
        $menu = Menu::findOrFail($id);
        
        // Check if menu has children
        if ($menu->children()->count() > 0) {
            session()->flash('error', 'Cannot delete menu with child items. Delete children first.');
            return;
        }
        
        $menu->roles()->detach();
        $menu->delete();
        
        // Generate JSON for all roles
        $this->menuService->clearMenuCache();
        
        session()->flash('message', 'Menu deleted successfully!');
    }
    
    public function toggleActive($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->update(['is_active' => !$menu->is_active]);
        
        // Generate JSON for all roles
        $this->menuService->clearMenuCache();
        
        session()->flash('message', 'Menu status updated!');
    }
    
    public function generateJson()
    {
        $this->generatedJson = json_encode($this->menuService->getUserMenus(), JSON_PRETTY_PRINT);    
        $this->showJson = true;
    }
    
    public function regenerateAllJson()
    {
        $this->menuService->generateJsonForAllRoles();
        session()->flash('message', 'JSON files regenerated for all roles!');
    }
    
    public function resetForm()
    {
        $this->reset([
            'menuId', 'name', 'icon', 'route', 'url', 
            'parent_id', 'order', 'permission_key', 'selectedRoles',
            'scheme_id', 'department_id'
        ]);
        $this->is_active = true;
        $this->resetValidation();
    }
    
    public function cancelForm()
    {
        $this->showForm = false;
        $this->resetForm();
    }
    
    public function closeJson()
    {
        $this->showJson = false;
    }
}