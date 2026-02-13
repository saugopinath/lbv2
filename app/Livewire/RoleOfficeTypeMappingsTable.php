<?php

namespace App\Livewire;

use App\Models\RoleOfficeTypeMapping;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class RoleOfficeTypeMappingsTable extends DataTableComponent
{
    public ?int $perPage = 5;
    public string $search = '';
    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setPaginationEnabled()
            ->setPerPageAccepted([5, 10, 25, 50])
            ->setPerPage($this->perPage)
            ->setPerPageVisibilityEnabled()
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
                ->searchable(),

            Column::make("Office Type", "officeType.name")
                ->searchable(),

            Column::make("Role", "role.name")
                ->searchable(),
            Column::make('Actions')
                ->label(fn($row) => view('coulmn_button.ConfirmDeleteButton', [
                    'itemId' => $row->id,
                    'action' => 'delete',
                    'title' => 'Delete RoleOfficeTypeMappings',
                    'message' => "This is $row->name , are you want to delete this RoleOfficeTypeMappings?",
                    'tooltip' => 'Delete RoleOfficeTypeMappings',

                ])->render())
                ->html(),
        ];
    }
    public function builder(): Builder
    {
        return RoleOfficeTypeMapping::with(['officeType', 'role']);
    }

    public function delete($id)
    {
        DB::transaction(function () use ($id) {
            RoleOfficeTypeMapping::where('id', $id)->delete();
        });

        $this->dispatch('notify', message: 'RoleOfficeTypeMappings deleted successfully!', type: 'success');
    }
}
