<?php

namespace App\Livewire;

use App\Models\Permission;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;

class PermissionListTableNew extends DataTableComponent
{
    protected $model = Permission::class;
    protected $listeners = ['refreshDatatable' => '$refresh'];
    public int $rowNumberOffset = 0;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->rowNumberOffset = ($this->getPage() - 1) * $this->getPerPage();

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
                'class' => 'px-4 py-3 text-white bg-violet-800 text-xs',
            ];
        });

        $this->setTdAttributes(function ($row) {
            return [
                'class' => 'px-4 py-3 text-gray-700 text-center',
            ];
        });

        $this->setTbodyAttributes([
            'class' => 'px-4 py-3 divide-y divide-gray-200 bg-white overflow-y-auto',
        ]);
        $this->setLoadingPlaceholderEnabled();
    }

    public function columns(): array
    {
        return [
            Column::make("No.")
                ->label(function ($value, $row) {
                    static $i = 0; // counter per page
                    $i++;
                    return ($this->getPage() - 1) * $this->getPerPage() + $i;
                }),
            Column::make("ID", "id")->hideIf(true),
            Column::make("Name", "name")->searchable(),
            Column::make("Parent", "parent_id")
                ->format(fn($value, $row) => $row->parent_id == null ? 'Parent' : $row->parent->name),
            Column::make("Created At", "created_at"),
            Column::make("Actions")
    ->label(fn($row) => '
        <button wire:click="delete(' . $row->id . ')"
            onclick="return confirm(\'Are you sure you want to delete permission: ' . addslashes($row->name) . '?\')"
            class="bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700 text-xs">
            Delete
        </button>
    ')
    ->html(),
        ];
    }

    public function delete($id)
    {

        $permission = Permission::find($id);
        // dd($permission);
        if ($permission) {
            $permission->delete();
            $this->dispatch('notify', message: 'Permission deleted successfully!');
            $this->dispatch('refreshDatatable');
        }
    }
}
