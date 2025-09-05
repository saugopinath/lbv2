<?php

namespace App\Livewire;

use App\Models\ApplicantIncompletDeatil;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use App\Models\Codemaster;


class IncompletTypeTable extends DataTableComponent
{
    public ?int $perPage = 5;
    public string $search = '';
    public ?string $filterCode = null;

    protected $listeners = ['filterIncompleteType' => 'applyFilter'];

    public function applyFilter($code)
    {
        $this->filterCode = $code;
        $this->resetPage();
    }

    public function configure(): void
    {
        $this->setPrimaryKey('application_id')
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
            Column::make("Application ID", "application_id")
                ->sortable()
                ->searchable(),

            Column::make("Incomplete Types")
                ->label(fn($row) => $row->incomplete_types_names ?? 'N/A')
                ->html()
                ->sortable()
                ->searchable(),

            Column::make("Name")
                ->label(fn($row) => $row->beneficiaryCommonList?->beneficiaryPersonal?->first()?->full_name ?? 'N/A'),

            Column::make("Father's Name")
                ->label(
                    fn($row) =>
                    $row->beneficiaryCommonList?->beneficiaryPersonal?->first()?->father?->first()?->full_name ?? 'N/A'
                ),

            Column::make("Address")
                ->label(function ($row) {
                    $common = $row->beneficiaryCommonList;

                    if ($common?->block_id && $common?->panchayat) {
                        return $common->panchayat->name; 
                    }

                    if ($common?->sub_division_id && $common?->ward) {
                        return $common->ward->name; 
                    }

                    return 'N/A';
                }),
        ];
    }

    public function builder(): Builder
    {
        $query = ApplicantIncompletDeatil::query()
            ->select('application_id')
            ->whereNull('next_level_request_id')
            ->groupBy('application_id')
            ->orderBy('application_id', 'asc')
            ->with([
                'beneficiaryCommonList.beneficiaryPersonal.father',
                'beneficiaryCommonList.block',
                'beneficiaryCommonList.panchayat',
                'beneficiaryCommonList.ward',
            ]);


        if ($this->filterCode) {
            $query->where('incomplet_type', $this->filterCode);
        }

        return $query;
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.incomplet-type-table', [
            'rows' => $this->getRows(),
        ]);
    }
}
