<?php

namespace App\Livewire;

use App\Helpers\CheckAuthHelper;
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
    public ?int $schemeId = null;
    public $district_id, $rural_urban, $blockurban, $gp_ward, $selectedSubdivision;

    // protected $listeners = ['doSearch' => 'doSearch'];
    protected $listeners = [
        'doSearch' => 'updateFilters',
    ];

    public $loginDistrictCode, $loginSubdivisionCode, $loginBlockCode;
    public array $filter_condition = [];
    public function mount(?int $schemeId = null, string $stage = ''): void
    {
        $this->stage = $stage;
        $this->schemeId = $schemeId;

        $select_lgd = session('lgd_session');

        if (!empty($select_lgd['district_id'])) {
            $this->filter_condition['district_id'] = Crypt::decryptString($select_lgd['district_id']);
        }

        if (!empty($select_lgd['block_id'])) {
            $this->filter_condition['block_id'] = Crypt::decryptString($select_lgd['block_id']);
        }

        if (!empty($select_lgd['subdivision_id'])) {
            $this->filter_condition['sub_division_id'] = Crypt::decryptString($select_lgd['subdivision_id']);
        }
    }

    public function updateFilters($filters)
    {
        // dd($filters);
        $this->district_id = $filters['district_id'] ?? null;
        $this->rural_urban = $filters['rural_urban'] ?? null;
        $this->selectedSubdivision = $filters['subdivision_id'] ?? null;
        $this->blockurban = $filters['blockurban'] ?? null;
        $this->gp_ward = $filters['gp_ward'] ?? null;
        $this->filterCode = $filters['incomplete_type'] ?? null;
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
        $columns = [
            Column::make("Application ID", "application_id")
                ->searchable(),

            Column::make("Incomplete Types")
                ->label(fn($row) => $row->incomplete_types_names ?? 'N/A')
                ->html(),

            Column::make("Name")
                ->label(
                    fn($row) =>
                    $row->personaldetails?->beneficiary_name ?? 'N/A'
                ),

            Column::make("Father's Name")
                ->label(
                    fn($row) =>
                    $row->personaldetails?->ben_father_name ?? 'N/A'
                ),
            Column::make("Address")
                ->label(
                    fn($row) =>
                    $row->contactdetails?->full_address ?? 'N/A'
                )
                ->html(),
        ];

        if ($this->stage === 'revert') {
            $columns[] = Column::make("Revert Reason")
                ->label(fn($row) => $row->acceptRejectInfo?->revertReason?->name ?? 'N/A')
                ->sortable();


            $columns[] = Column::make("Revert Remarks")
                ->label(fn($row) => $row->acceptRejectInfo?->revert_reason_remarks ?? 'N/A')
                ->sortable();
        }

        $columns[] = Column::make("Actions")
            ->label(function ($row) {
                $stage = request()->get('stage');               

                $buttonText = match ($stage) {
                    'approver', 'revert' => 'View',
                    default => 'Update',
                };

                $link = route('incomplet-type.view', [
                    'id' => Crypt::encryptString($row->application_id),
                    'stage' => Crypt::encryptString($this->stage),
                    'schemeId' => Crypt::encryptString( $this->schemeId),
                ]);

                return view('coulmn_button.view', [
                    'link' => $link,
                    'tooltip' => $buttonText,
                    'text' => $buttonText,
                ])->render();
            })
            ->html();

        return $columns;
    }

    public function builder(): Builder
    {
        $query = ApplicantIncompletDeatil::applicationWise($this->schemeId);

        $stage = $this->stage ?? null;

        if (!$stage) {

            if (CheckAuthHelper::isCommmonVerifier()) {
                $stage = 'verifier';

            } elseif (CheckAuthHelper::isCommonApprover()) {
                $stage = 'approver';
            }
        }

        switch ($stage) {
            case 'verifier':
                $query->whereNull('next_level_request_id');
                break;

            case 'approver':
                $query->where('next_level_request_id', 1);
                break;

            case 'revert':
                $query->where('next_level_request_id', -50)
                    ->with([
                        'acceptRejectInfo' => function ($q) {
                            $q->latest('id');
                        }
                    ]);
                break;
        }

        // Location filter apply

        // if ($this->district_id || $this->rural_urban || $this->blockurban || $this->gp_ward || $this->filterCode) {
        //     $query = EncryptionArray::applyIncompletLocationFilter(
        //         $query,
        //         $this->district_id ? (int) $this->district_id : null,
        //         $this->rural_urban ? (int) $this->rural_urban : null,
        //         $this->blockurban ? (int) $this->blockurban : null,
        //         $this->gp_ward ? (int) $this->gp_ward : null,
        //         $this->filterCode ? (int) $this->filterCode : null,
        //     );
        // }
        // dd($query->first());
        return $query;
    }
}
