<?php

namespace App\Livewire;

use App\Models\RoleMenuUserMapping;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;

class RoleMenuUserMappingTable extends DataTableComponent
{
    public ?int $perPage = 10;
    public string $search = '';
    
    protected $listeners = ['refreshDatatable' => '$refresh'];

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setPaginationEnabled()
            ->setPerPageAccepted([10, 25, 50])
            ->setPerPage($this->perPage)
            ->setSearchEnabled()
            ->setSearchLive()
            ->setBulkActionsEnabled();

        $this->setHideBulkActionsWhenEmptyEnabled();

        $this->setTableWrapperAttributes([
            'class' => 'overflow-x-auto overflow-y-auto max-h-[500px] border rounded-lg shadow-sm',
        ]);

        $this->setTableAttributes([
            'class' => 'min-w-full text-sm text-gray-700 text-center overflow-x-auto',
        ]);

        $this->setTheadAttributes([
            'class' => 'bg-violet-800 text-xs uppercase py-3 px-4 text-white',
        ]);
        
        $this->setThAttributes(function ($column) {
            return [
                'class' => 'px-4 py-3 text-white bg-violet-800 text-xs text-left',
            ];
        });

        $this->setTdAttributes(function ($row) {
            return [
                'class' => 'px-4 py-3 text-gray-700 text-left',
            ];
        });

        $this->setTbodyAttributes([
            'class' => 'px-4 py-3 divide-y divide-gray-200 bg-white overflow-y-auto',
        ]);
    }

    public function updatedSearch($value): void
    {
        $this->setSearch($value);
        $this->resetPage();
    }

    public function updatedPerPage($value): void
    {
        $this->perPage = (int) $value;
        $this->setPerPage((int) $value);
        $this->resetPage();
    }

    public function columns(): array
    {
        return [
            Column::make("ID", "id")
                ->searchable()
                ->sortable(),

            Column::make("Menu Name", "menu.menu_name")
                ->searchable()
                ->sortable(),

            Column::make("Role", "role.name")
                ->searchable()
                ->sortable(),
                
            Column::make("Scheme", "scheme.name")
                ->searchable()
                ->sortable(),
                
            Column::make("Department", "department.name")
                ->searchable()
                ->sortable(),

            Column::make("Permission", "permission.name")
                ->searchable()
                ->sortable(),

            Column::make('Actions')
                ->label(fn($row) => '<button type="button" wire:click="$dispatch(\'editMapping\', { id: ' . $row->id . ' })" class="px-3 py-1 text-xs text-white bg-blue-600 rounded hover:bg-blue-700">Edit</button>')
                ->html(),
        ];
    }

    public function builder(): Builder
    {
        return RoleMenuUserMapping::with(['menu', 'role', 'scheme', 'department', 'permission']);
    }
}
