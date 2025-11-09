<?php

namespace App\Livewire;

use App\Models\OfficeMaster;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;

class OfficeMasters extends DataTableComponent
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
            Column::make("Name", "name")
                ->sortable()
                ->searchable(),
            Column::make("Address", "address")
                ->sortable()
                ->searchable(),
            Column::make("Zip", "zip")
                ->sortable()
                ->searchable(),
            Column::make("Office Type", "officeType.name")
                ->sortable()
                ->searchable(),
        ];
    }

    public function builder(): Builder
    {
        return OfficeMaster::with(['officeType']);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.office-masters-table', [
            'rows' => $this->getRows(),
        ]);
    }
}
