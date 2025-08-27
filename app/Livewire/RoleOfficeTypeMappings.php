<?php

namespace App\Livewire;

use App\Models\RoleOfficeTypeMapping;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;

class RoleOfficeTypeMappings extends DataTableComponent
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
    public function delete($id): void
    {
        $record = RoleOfficeTypeMapping::find($id);

        if ($record) {
            $record->delete();
            session()->flash('message', 'Record deleted successfully.');
            $this->resetPage();
        } else {
            session()->flash('error', 'Record not found.');
        }
    }


    public function render(): \Illuminate\View\View
    {
        return view('livewire.role-office-type-mappings-table', [
            'rows' => $this->getRows(),
        ]);
    }
}
