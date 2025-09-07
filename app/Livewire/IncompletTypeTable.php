<?php

namespace App\Livewire;

use App\Models\ApplicantIncompletDeatil;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Illuminate\Support\Facades\Crypt;
use App\Helpers\EncryptionArray;
use Rappasoft\LaravelLivewireTables\DataTableComponent;


class IncompletTypeTable extends DataTableComponent
{
    public ?int $perPage = 5;
    public string $search = '';
    public ?string $filterCode = null;
    public string $stage = '';

    public $district_id, $rural_urban, $blockurban, $gp_ward, $selectedSubdivision;
    protected $listeners = ['filterIncompleteType' => 'applyFilter', 'filtersApplied'];

    public $loginDistrictCode, $loginSubdivisionCode, $loginBlockCode;
    public array $filter_condition = [];
    public function mount(string $stage = ''): void
    {
        $this->stage = $stage;

        $select_lgd = session('lgd_session');

        if (!empty($select_lgd['district_id'])) {
            $this->filter_condition['district_id'] = Crypt::decryptString($select_lgd['district_id']);
        }

        if (!empty($select_lgd['block_id'])) {
            $this->filter_condition['block_id'] = Crypt::decryptString($select_lgd['block_id']);
        }

        if (!empty($select_lgd['subdivision_id'])) {
            $this->filter_condition['subdivision_id'] = Crypt::decryptString($select_lgd['subdivision_id']);
        }
    }

    public function applyFilter($code)
    {
        $this->filterCode = $code;
        $this->resetPage();
    }

    public function filtersApplied($filters)
    {
        $this->district_id = $filters['district_id'] ?? null;
        $this->rural_urban = $filters['rural_urban'] ?? null;
        $this->blockurban = $filters['blockurban'] ?? null;
        $this->gp_ward = $filters['gp_ward'] ?? null;
        $this->selectedSubdivision = $filters['subdivision_id'] ?? null;

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
                ->label(
                    fn($row) =>
                    $row->beneficiaryCommonList?->beneficiaryPersonal?->full_name ?? 'N/A'
                ),

            Column::make("Father's Name")
                ->label(
                    fn($row) =>
                    $row->beneficiaryCommonList?->beneficiaryPersonal?->father?->first()?->full_name
                        ?? 'N/A'
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
            ->groupBy('application_id')
            ->orderBy('application_id', 'asc');

        if ($this->stage === 'verifier') {
            $query->whereNull('next_level_request_id');
        } elseif ($this->stage === 'approver') {
            $query->where('next_level_request_id', 1);
        } elseif ($this->stage === 'revert') {
            $query->where('next_level_request_id', -50);
        }

        if ($this->district_id || $this->rural_urban || $this->blockurban || $this->gp_ward) {
            $query = EncryptionArray::applyLocationFilter(
                $query,
                $this->district_id ? (int) $this->district_id : null,
                $this->rural_urban ? (int) $this->rural_urban : null,
                $this->blockurban ? (int) $this->blockurban : null,
                $this->gp_ward ? (int) $this->gp_ward : null
            );
        }

        if ($this->filterCode) {
            $query->where('incomplet_type', $this->filterCode);
        }

        return $query;
    }

    public function getActiveFiltersProperty()
    {
        $filters = [];

        if ($this->district_id) {
            $filters[] = 'District: ' . (\App\Models\District::find($this->district_id)?->name ?? $this->district_id);
        }

        if ($this->rural_urban) {
            $filters[] = 'Rural/Urban: ' . ($this->rural_urban == 2 ? 'Rural' : 'Urban');
        }

        // Rural
        if ($this->rural_urban == 2 && $this->blockurban) {
            $filters[] = 'Block: ' . (\App\Models\Block::find($this->blockurban)?->name ?? $this->blockurban);
            if ($this->gp_ward) {
                $filters[] = 'GP: ' . (\App\Models\Panchayat::find($this->gp_ward)?->name ?? $this->gp_ward);
            }
        }

        // Urban
        if ($this->rural_urban == 1 && $this->selectedSubdivision) {
            $filters[] = 'Subdivision: ' . (\App\Models\Subdivision::find($this->selectedSubdivision)?->name ?? $this->selectedSubdivision);
            if ($this->blockurban) {
                $filters[] = 'Municipality: ' . (\App\Models\Municipality::find($this->blockurban)?->name ?? $this->blockurban);
            }
            if ($this->gp_ward) {
                $filters[] = 'Ward: ' . (\App\Models\Ward::find($this->gp_ward)?->name ?? $this->gp_ward);
            }
        }

        return implode(', ', $filters);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.incomplet-type-table', [
            'rows' => $this->getRows(),
            'stage' => $this->stage,
        ]);
    }
}
