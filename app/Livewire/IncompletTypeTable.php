<?php

namespace App\Livewire;

use App\Models\User;
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
            Column::make("Application ID", "application_id")->sortable()->searchable(),
            Column::make("Incomplete Types")
                ->label(fn($row) => $row->incomplete_types_names ?? 'N/A')
                ->html()->sortable()->searchable(),
        ];
    }

    public function builder(): Builder
    {
           $relationFather = Codemaster::getIdByCode(131);
        // $query = ApplicantIncompletDeatil::query()
        //     ->select('application_id')
        //     ->where('next_level_request_id', 1)
        //     ->groupBy('application_id');

        $query = ApplicantIncompletDeatil::query()
        ->select('application_id')
        ->where('next_level_request_id', 1)
        ->groupBy('application_id')
        ->with([
            'beneficiaryCommonList.beneficiaryPersonal' => function ($q) use ($relationFather) {
                $q->with([
                    'father' => fn($q) => $q->where('relation_type_id', $relationFather)
                ])->whereHas('father', fn($q) => $q->where('relation_type_id', $relationFather));
            }
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
