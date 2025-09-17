<?php

namespace App\Livewire;

use App\Models\Ward;
use App\Models\Block;
use App\Models\District;
use App\Models\Panchayat;
use App\Models\Subdivision;
use App\Models\Municipality;
use App\Models\Codemaster;
use App\Helpers\EncryptionArray;
use Illuminate\Support\Facades\Crypt;
use App\Models\ApplicantIncompletDeatil;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;


class IncompletTypeTable extends DataTableComponent
{
    public ?int $perPage = 5;
    public string $search = '';
    public string $stage = '';
    public ?string $filterCode = null;
    public $district_id, $rural_urban, $blockurban, $gp_ward, $selectedSubdivision;

    protected $listeners = ['doSearch' => 'doSearch'];

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

    public function doSearch(array $filters)
    {
        $this->district_id = $filters['district_id'] ?? null;
        $this->rural_urban = $filters['rural_urban'] ?? null;
        $this->selectedSubdivision = $filters['subdivision_id'] ?? null;
        $this->blockurban = $filters['blockurban'] ?? null;
        $this->gp_ward = $filters['gp_ward'] ?? null;
        $this->filterCode = $filters['incomplete_type'] ?? null;
        // dd($this->filterCode);
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
        $columns = [
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

        if ($this->stage === 'revert') {
            $columns[] = Column::make("Revert Reason")
                ->label(fn($row) => $row->acceptRejectInfo?->revertReason?->name ?? 'N/A')
                ->sortable();


            $columns[] = Column::make("Revert Remarks")
                ->label(fn($row) => $row->acceptRejectInfo?->revert_reason_remarks ?? 'N/A')
                ->sortable();
        }

        return $columns;
    }

    public function builder(): Builder
{
    $query = ApplicantIncompletDeatil::query()
        ->select('application_id')
        ->groupBy('application_id')
        ->orderBy('application_id', 'asc');

    $user = auth()->user();

    $next_level_request_id = null;

    // 👉 প্রথমে route / livewire থেকে আসা $this->stage ব্যবহার করো
    $stage = $this->stage ?? null;

    // যদি radio দিয়ে বা route থেকে না আসে, fallback হবে role অনুযায়ী
    if (!$stage) {
        if ($user->hasAnyRole(['Verifier', 'Delegated Verifier'])) {
            $stage = 'verifier';
            $next_level_request_id = null;
        } elseif ($user->hasAnyRole(['Approver', 'Delegated Approver'])) {
            $stage = 'approver';
            $next_level_request_id = 1;
        }
    }

    switch ($stage) {
        case 'verifier':
            $query->whereNull('next_level_request_id');
            break;

        case 'approver':
            $query->where('next_level_request_id', 1);
            break;

        case 'revert': // ✅ এখানে কাজ করবে
            $query->where('next_level_request_id', -50)
                ->with(['acceptRejectInfo' => function ($q) {
                    $q->latest('id');
                }]);
            break;
    }

    // Location filter apply
    if ($this->district_id || $this->rural_urban || $this->blockurban || $this->gp_ward || $this->filterCode) {
        $query = EncryptionArray::applyLocationFilter(
            $query,
            $this->district_id ? (int) $this->district_id : null,
            $this->rural_urban ? (int) $this->rural_urban : null,
            $this->blockurban ? (int) $this->blockurban : null,
            $this->gp_ward ? (int) $this->gp_ward : null,
            $this->filterCode ? (int) $this->filterCode : null,
        );
    }

    return $query;
}


    // public function builder(): Builder
    // {
    //     $query = ApplicantIncompletDeatil::query()
    //         ->select('application_id')
    //         ->groupBy('application_id')
    //         ->orderBy('application_id', 'asc');

    //     $user = auth()->user();

    //     $next_level_request_id = null;
    //     $stage = null;

    //     if ($user->hasAnyRole(['Verifier', 'Delegated Verifier'])) {
    //         $stage = 'verifier';
    //         $next_level_request_id = null;
    //     } elseif ($user->hasAnyRole(['Approver', 'Delegated Approver'])) {
    //         $stage = 'approver';
    //         $next_level_request_id = 1;
    //     }

    //     switch ($stage) {
    //         case 'verifier':
    //             $query->whereNull('next_level_request_id');
    //             break;

    //         case 'approver':
    //             $query->where('next_level_request_id', 1);
    //             break;

    //         case 'revert':
    //             $query->where('next_level_request_id', -50)
    //                 ->with(['acceptRejectInfo' => function ($q) {
    //                     $q->latest('id');
    //                 }]);
    //             break;
    //     }


    //     if ($this->district_id || $this->rural_urban || $this->blockurban || $this->gp_ward || $this->filterCode) {
    //         $query = EncryptionArray::applyLocationFilter(
    //             $query,
    //             $this->district_id ? (int) $this->district_id : null,
    //             $this->rural_urban ? (int) $this->rural_urban : null,
    //             $this->blockurban ? (int) $this->blockurban : null,
    //             $this->gp_ward ? (int) $this->gp_ward : null,
    //             $this->filterCode ? (int) $this->filterCode : null,
    //         );
    //     }

    //     return $query;
    // }
    public function getActiveFiltersProperty()
    {
        $filters = [];

        // GP / Ward
        if ($this->gp_ward) {
            if ($this->rural_urban == 1) {
                $ward = Ward::find($this->gp_ward)?->name;
                if ($ward) $filters[] = 'Ward: ' . $ward;
            } else {
                $gp = Panchayat::find($this->gp_ward)?->name;
                if ($gp) $filters[] = 'GP: ' . $gp;
            }
        }

        // Block / Municipality
        if ($this->blockurban) {
            if ($this->rural_urban == 2) {
                $block = Block::find($this->blockurban)?->name;
                if ($block) $filters[] = 'Block: ' . $block;
            } else {
                $municipality = Municipality::find($this->blockurban)?->name;
                if ($municipality) $filters[] = 'Municipality: ' . $municipality;
            }
        }

        // Subdivision
        if ($this->selectedSubdivision) {
            $sub = Subdivision::find($this->selectedSubdivision)?->name;
            if ($sub) $filters[] = 'Subdivision: ' . $sub;
        }

        // District
        if ($this->district_id) {
            $district = District::find($this->district_id)?->name;
            if ($district) $filters[] = 'District: ' . $district;
        }

        // Rural / Urban
        if ($this->rural_urban) {
            $filters[] = 'Rural/Urban: ' . ($this->rural_urban == 2 ? 'Rural' : 'Urban');
        }

        // Incomplete Type
        if ($this->filterCode) {
            $codemaster = Codemaster::where('code', $this->filterCode)->first()?->name;
            if ($codemaster) $filters[] = 'Incomplete Type: ' . $codemaster;
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
