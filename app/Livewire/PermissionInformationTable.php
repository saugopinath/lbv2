<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Permission;
use App\Attributes\Loggable;

class PermissionInformationTable extends Component
{
    use WithPagination;

    // Filters & Pagination
    public string $search = '';
    public int $perPage = 10;
    public string $parentFilter = '';
    public string $guardFilter = '';

    // Lists for filter dropdowns
    public array $parentList = [];
    public array $guardList = [];

    // Edit Modal State
    public bool $showEditModal = false;
    public ?int $editingPermissionId = null;
    public string $editingPermissionName = '';
    public string $editingPermissionParentName = '';
    public string $editingPermissionGuard = '';
    public string $editDescription = '';

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $all = Permission::select('id', 'name', 'parent_id', 'guard_name')->get();
        $parentIds = $all->whereNotNull('parent_id')->pluck('parent_id')->unique()->flip();
        $this->parentList = $all
            ->filter(fn($p) => isset($parentIds[$p->id]))
            ->sortBy('name')
            ->pluck('name', 'id')
            ->toArray();

        $this->guardList = $all
            ->pluck('guard_name')
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->toArray();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function updatingParentFilter()
    {
        $this->resetPage();
    }

    public function updatingGuardFilter()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'parentFilter', 'guardFilter']);
        $this->resetPage();
    }

    public function openEditModal($id)
    {
        $this->resetValidation();
        $permission = Permission::select('id', 'name', 'parent_id', 'guard_name', 'description')
            ->with('parent:id,name')
            ->find($id);

        if (!$permission) {
            $this->dispatch('toastr', [
                'type'    => 'error',
                'message' => 'Permission not found.'
            ]);
            return;
        }

        $this->editingPermissionId = $permission->id;
        $this->editingPermissionName = $permission->name;
        $this->editingPermissionParentName = $permission->parent ? $permission->parent->name : 'Top-Level (Parent)';
        $this->editingPermissionGuard = $permission->guard_name;
        $this->editDescription = $permission->description ?? '';
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->reset(['editingPermissionId', 'editingPermissionName', 'editingPermissionParentName', 'editingPermissionGuard', 'editDescription']);
        $this->resetValidation();
    }

    #[Loggable(level: 'U', nickname: 'Permission Description Update')]
    public function updateDescription()
    {
        if (!$this->editingPermissionId) {
            return;
        }

        $this->validate([
            'editDescription' => 'nullable|string|max:1000',
        ], [
            'editDescription.max' => 'Description cannot exceed 1000 characters.',
        ]);

        $permission = Permission::select('id', 'name', 'description')->find($this->editingPermissionId);

        if ($permission) {
            $permission->description = !empty(trim($this->editDescription)) ? trim($this->editDescription) : null;
            $permission->save();

            $this->closeEditModal();
            $this->dispatch('toastr', [
                'type'    => 'success',
                'message' => "Description for permission '{$permission->name}' updated successfully!"
            ]);
        } else {
            $this->dispatch('toastr', [
                'type'    => 'error',
                'message' => 'Permission record not found.'
            ]);
        }
    }

    public function render()
    {
        $query = Permission::with(['parent:id,name'])
            ->select('permissions.id', 'permissions.name', 'permissions.parent_id', 'permissions.guard_name', 'permissions.description');

        if (!empty(trim($this->search))) {
            $searchTerm = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('permissions.name', 'ILIKE', $searchTerm)
                    ->orWhere('permissions.guard_name', 'ILIKE', $searchTerm)
                    ->orWhere('permissions.description', 'ILIKE', $searchTerm)
                    ->orWhereRaw("CAST(permissions.id AS TEXT) ILIKE ?", [$searchTerm])
                    ->orWhereHas('parent', function ($parentQ) use ($searchTerm) {
                        $parentQ->where('name', 'ILIKE', $searchTerm);
                    });
            });
        }

        if ($this->parentFilter === 'root') {
            $query->whereNull('permissions.parent_id');
        } elseif (!empty($this->parentFilter) && is_numeric($this->parentFilter)) {
            $query->where('permissions.parent_id', (int) $this->parentFilter);
        }

        if (!empty($this->guardFilter)) {
            $query->where('permissions.guard_name', $this->guardFilter);
        }

        $permissions = $query->orderBy('permissions.id', 'asc')
            ->paginate($this->perPage);

        return view('livewire.permission-information-table', [
            'permissions' => $permissions,
        ]);
    }
}
