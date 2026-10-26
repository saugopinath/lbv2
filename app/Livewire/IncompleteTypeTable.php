<?php

namespace App\Livewire;

use App\Helpers\CheckAuthHelper;
use App\Services\TableExportService;
use App\Helpers\EncryptionArray;
use Illuminate\Support\Facades\Crypt;
use App\Models\ApplicantIncompleteDetail;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;

class IncompleteTypeTable extends DataTableComponent
{
    public ?int $perPage = 5;
    public string $search = '';
    public string $stage = '';
    public ?int $schemeId = null;
    public $district_id, $rural_urban, $blockurban, $gpward, $selectedSubdivision, $filterCode;
    public $searchApplicationId, $searchApplicantName, $searchMobile;

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
            $this->filter_condition['created_by_dist_code'] = Crypt::decryptString($select_lgd['district_id']);
        }

        if (!empty($select_lgd['block_id'])) {
            $this->filter_condition['created_by_local_body_code'] = Crypt::decryptString($select_lgd['block_id']);
        }

        if (!empty($select_lgd['subdivision_id'])) {
            $this->filter_condition['created_by_local_body_code'] = Crypt::decryptString($select_lgd['subdivision_id']);
        }
    }

    public function updateFilters($filters)
    {
        $this->district_id = $filters['district_id'] ?? null;
        $this->rural_urban = $filters['rural_urban'] ?? null;
        $this->selectedSubdivision = $filters['subdivision_id'] ?? null;
        $this->blockurban = $filters['blockurban'] ?? null;
        $this->gpward = $filters['gpward'] ?? null;
        $this->filterCode = $filters['incomplete_type'] ?? null;
        $this->searchApplicationId = $filters['application_id'] ?? null;
        $this->searchApplicantName = $filters['applicant_name'] ?? null;
        $this->searchMobile = $filters['mobile_no'] ?? null;

        $this->resetPage();
    }

    public function configure(): void
    {
        $this->setPrimaryKey('application_id')
            ->setPaginationEnabled()
            ->setPerPageAccepted([5, 10, 25, 50])
            ->setDefaultPerPage(5)
            ->setPerPage($this->perPage ?? 5)
            ->setPerPageVisibilityEnabled()
            ->setSortingDisabled()
            ->setSearchEnabled()
            ->setSearchLive()
            ->setSearchPlaceholder('Search by ID, Name, Mobile, Type...')
            ->setBulkActionsEnabled();

        $this->setHideBulkActionsWhenEmptyEnabled();

        $this->setConfigurableAreas([
            'toolbar-left-start' => 'livewire.export_excel_buttons',
        ]);

        $this->setTableWrapperAttributes([
            'class' => 'overflow-x-auto overflow-y-auto max-h-[500px] border rounded-lg shadow-sm w-full max-w-full',
        ]);

        $this->setTableAttributes([
            'class' => 'min-w-full text-sm text-gray-700 text-center overflow-x-auto',
        ]);

        $this->setTheadAttributes([
            'class' => 'bg-violet-800 text-xs uppercase py-3 px-4 text-white',
        ]);
        $this->setThAttributes(function ($column) {
            return [
                'class' => 'px-3 py-3 text-white bg-violet-800 text-xs font-semibold tracking-wider text-center',
            ];
        });

        $this->setTdAttributes(function ($row) {
            return [
                'class' => 'px-3 py-2 text-gray-700 text-center align-middle',
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
                // ->sortable()
                ->label(fn($row) => '<span class="font-semibold text-violet-700 whitespace-nowrap">' . $row->application_id . '</span>')
                ->html()
                ->searchable(function (Builder $query, $searchTerm) {
                    $query->where('applicant_incomplete_details.application_id', 'like', '%' . trim($searchTerm) . '%');
                }),

            Column::make("Incomplete Types")
                ->label(function ($row) {
                    $raw = $row->incomplete_types_names;
                    if (empty($raw)) {
                        return '<span class="text-xs text-gray-400 italic">None</span>';
                    }
                    $items = explode('<br>', $raw);
                    $badges = array_map(function ($item) {
                        return '<div class="py-0.5"><span class="inline-block px-2 py-0.5 text-xs font-medium rounded bg-amber-50 text-amber-800 border border-amber-200 text-left max-w-[220px] break-words whitespace-normal leading-snug">' . e($item) . '</span></div>';
                    }, $items);
                    return '<div class="flex flex-col items-center justify-center">' . implode('', $badges) . '</div>';
                })
                ->html()
                ->searchable(function (Builder $query, $searchTerm) {
                    $query->orWhereHas('incompleteType', function (Builder $q) use ($searchTerm) {
                        $q->where('name', 'ilike', '%' . trim($searchTerm) . '%');
                    });
                }),

            Column::make("Name")
                ->label(
                    fn($row) =>
                    '<span class="font-medium text-gray-800 whitespace-nowrap">' .
                    e($row->personaldetails?->beneficiary_name ?? 'N/A') .
                    '</span>'
                )
                ->html()
                ->searchable(function (Builder $query, $searchTerm) {
                    $query->orWhereHas('personaldetails', function (Builder $q) use ($searchTerm) {
                        $q->where('beneficiary_name', 'ilike', '%' . trim($searchTerm) . '%');
                    });
                }),

            Column::make("Father's Name")
                ->label(
                    fn($row) =>
                    '<span class="text-gray-600 whitespace-nowrap">' .
                    e($row->personaldetails?->ben_father_name ?? 'N/A') .
                    '</span>'
                )
                ->html()
                ->searchable(function (Builder $query, $searchTerm) {
                    $query->orWhereHas('personaldetails', function (Builder $q) use ($searchTerm) {
                        $q->where('ben_father_name', 'ilike', '%' . trim($searchTerm) . '%');
                    });
                }),

            Column::make("Address")
                ->label(
                    fn($row) =>
                    '<div class="max-w-[180px] mx-auto text-xs text-gray-600 break-words whitespace-normal leading-relaxed">' .
                    ($row->contactdetails?->full_address ?? 'N/A') .
                    '</div>'
                )
                ->html()
                ->searchable(function (Builder $query, $searchTerm) {
                    $query->orWhereHas('contactdetails', function (Builder $q) use ($searchTerm) {
                        $term = trim($searchTerm);
                        $q->where('villtowncity', 'ilike', "%{$term}%")
                          ->orWhere('pincode', 'like', "%{$term}%")
                          ->orWhere('postoffice', 'ilike', "%{$term}%")
                          ->orWhere('policestation', 'ilike', "%{$term}%");
                    });
                }),
        ];

        if ($this->stage === 'revert') {
            $columns[] = Column::make("Revert Reason")
                ->label(function ($row) {
                    $reason = $row->acceptRejectInfo?->revertReason?->name;
                    if (!$reason) {
                        return '<span class="text-xs text-gray-400 italic">N/A</span>';
                    }
                    return '<div class="max-w-[160px] mx-auto text-xs font-medium text-rose-700 bg-rose-50 border border-rose-200 rounded px-2 py-1 break-words whitespace-normal leading-snug">' . e($reason) . '</div>';
                })
                ->html();
                // ->sortable();

            $columns[] = Column::make("Revert Remarks")
                ->label(function ($row) {
                    $remarks = $row->acceptRejectInfo?->revert_reason_remarks;
                    if (!$remarks) {
                        return '<span class="text-xs text-gray-400 italic">None</span>';
                    }
                    return '<div class="max-w-[180px] mx-auto text-xs text-gray-700 break-words whitespace-normal leading-relaxed text-left">' . e($remarks) . '</div>';
                })
                ->html();
                // ->sortable();
        }

        $columns[] = Column::make("Actions")
            ->label(function ($row) {
                $currentStage = $this->stage ?? request()->get('stage') ?? 'verifier';

                $buttonText = match ($currentStage) {
                    'approver', 'revert' => 'View',
                    default => 'Update',
                };

                $link = route('incomplete-type.view', [
                    'id' => Crypt::encryptString($row->application_id),
                    'stage' => Crypt::encryptString($currentStage),
                    'schemeId' => Crypt::encryptString($this->schemeId),
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
        $query = ApplicantIncompleteDetail::applicationWise($this->schemeId);

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

        if (!empty($this->filter_condition)) {
            $query->whereHas('personaldetails', function ($q) {
                $q->where($this->filter_condition);
            });
        }

        // Direct search filters from panel
        if (!empty($this->searchApplicationId)) {
            $query->where('applicant_incomplete_details.application_id', 'like', '%' . trim($this->searchApplicationId) . '%');
        }

        if (!empty($this->searchApplicantName)) {
            $query->whereHas('personaldetails', function ($q) {
                $q->where('beneficiary_name', 'ilike', '%' . trim($this->searchApplicantName) . '%');
            });
        }

        if (!empty($this->searchMobile)) {
            $query->whereHas('contactdetails', function ($q) {
                $q->where('mobile_no', 'like', '%' . trim($this->searchMobile) . '%');
            });
        }

        if ($this->district_id || $this->rural_urban || $this->blockurban || $this->gpward || $this->filterCode) {
            $query = EncryptionArray::applyIncompleteLocationFilter(
                $query,
                $this->district_id ? (int) $this->district_id : null,
                $this->rural_urban ? (int) $this->rural_urban : null,
                $this->blockurban ? (int) $this->blockurban : null,
                $this->gpward ? (int) $this->gpward : null,
                $this->selectedSubdivision ? (int) $this->selectedSubdivision : null,
                $this->filterCode ? (int) $this->filterCode : null,
            );
        }

        $this->dispatch('hideLoader');
        return $query;
    }

    public function exportExcel(TableExportService $exportService)
    {
        return $exportService->export(
            $this,
            'applications_all.xlsx'
        );
    }
}
