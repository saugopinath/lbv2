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
            ->setSearchLive();
    }
    public function updatedSearch($value): void
    {
        $this->setSearch($value);
        $this->resetPage();
    }
    public function updatedPerPage($value): void
    {
        $this->perPage = (int)$value;
        $this->setPerPage((int)$value);
        $this->resetPage();
    }

    public function columns(): array
    {
        return [
            Column::make("ID", "id")
                ->sortable()
                ->searchable(),

            Column::make("Office Type", "officeType.name")
                ->sortable()
                ->searchable(),

            Column::make("Role", "role.name")
                ->sortable()
                ->searchable(),
            Column::make('Actions')
                ->label(function ($row) {
                    return view('livewire.role-office-type-mappings-table', ['row' => $row]);
                }),
        ];
    }
    public function builder(): Builder
    {
        return RoleOfficeTypeMapping::with(['officeType', 'role']);
    }
    public function deleteUser($userId)
    {
        DB::transaction(function () use ($userId) {
            RoleOfficeTypeMapping::where('id', $userId)->delete();
        });

        Session::flash('success', 'User deleted successfully.');
    }
    public function render(): \Illuminate\View\View
    {
        return view('livewire.role-office-type-mappings-table', [
            'rows' => $this->getRows(),
        ]);
    }
}
