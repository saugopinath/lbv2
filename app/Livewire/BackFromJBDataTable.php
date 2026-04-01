<?php

namespace App\Livewire;

use App\Helpers\CheckAuthHelper;
use App\Helpers\EncryptionArray;
use App\Models\BackFromJb;
use App\Models\Codemaster;
use App\Services\TableExportService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Crypt;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class BackFromJBDataTable extends DataTableComponent
{
    public ?int $perPage = 5;

    protected $listeners = [
        'doSearch' => 'updateFilters',
    ];

    public $district_id;

    public $rural_urban;

    public $blockurban;

    public $gp_ward;

    public $next_level_role_id;

    public $revertrejectAction;

    public $revertrejectCauses;

    public $sub_div;

    public $is_filtered_reset;

    public array $filter_condition = [];

    public function mount(): void
    {
        // if ($this->is_filtered_reset) {
        if (CheckAuthHelper::isCommmonVerifier()) {
            $this->next_level_role_id = Codemaster::getIdByCode(4401);
        } elseif (CheckAuthHelper::isCommonApprover()) {
            $this->next_level_role_id = Codemaster::getIdByCode(4402);
        }
        // }

        $select_lgd = session('lgd_session');

        if (! empty($select_lgd['district_id'])) {
            $this->filter_condition['created_by_dist_code'] = Crypt::decryptString($select_lgd['district_id']);
        }

        if (! empty($select_lgd['block_id'])) {
            $this->filter_condition['created_by_local_body_code'] = Crypt::decryptString($select_lgd['block_id']);
        }

        if (! empty($select_lgd['subdivision_id'])) {
            $this->filter_condition['created_by_local_body_code'] = Crypt::decryptString($select_lgd['subdivision_id']);
        }
    }

    public function configure(): void
    {
        $this->setPrimaryKey('application_id')
            ->setPaginationEnabled()
            ->setPerPageAccepted([5, 10])
            ->setPerPage($this->perPage)
            ->setPerPageVisibilityEnabled()
            ->setSearchEnabled()
            ->setSearchLive()
            ->setColumnSelectDisabled();

        $this->setConfigurableAreas([
            'toolbar-left-start' => 'livewire.export_excel_buttons',
        ]);
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

    // public function updateFilters($filters)
    // {
    //     $this->resetPage();
    // }
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

    public function updateFilters($filters)
    {
        $this->district_id = $filters['district_id'];
        $this->rural_urban = $filters['rural_urban'] ?? null;
        $this->blockurban = $filters['blockurban'];
        $this->gp_ward = $filters['gpward'];
        $this->next_level_role_id = $filters['application_type'];
        if ($this->next_level_role_id == null) {
            if (CheckAuthHelper::isCommmonVerifier()) {
                $this->next_level_role_id = Codemaster::getIdByCode(4401);
            } elseif (CheckAuthHelper::isCommonApprover()) {
                $this->next_level_role_id = Codemaster::getIdByCode(4402);
            }
        }
    }

    public function columns(): array
    {
        return [
            Column::make('Application ID', 'application_id')
                ->label(fn ($row) => $row->beneficiary->application_id ?? 'N/A')
                ->sortable()
                ->searchable(function ($query, $searchTerm) {
                    $query->whereHas('beneficiary', function ($q) use ($searchTerm) {
                        $q->where('application_id', 'ILIKE', "%{$searchTerm}%");
                    });
                }),

            Column::make('Applicant Name', 'full_name')
                ->label(fn ($row) => $row->beneficiary->beneficiary_name ?? 'N/A')
                ->searchable(function ($query, $searchTerm) {
                    $query->whereHas('beneficiary', function ($q) use ($searchTerm) {
                        $q->where('full_name', 'ILIKE', "%{$searchTerm}%");
                    });
                }),

            Column::make('Mobile No', 'Mobile No')
                ->label(fn ($row) => $row->beneficiary->other_details['mobile_no']
                    ?? 'N/A'),

            Column::make('Address', 'Address')
                ->label(fn ($row) => $row->beneficiary->contact->getFullAddress() ?? 'N/A')
                ->html(),

            Column::make('Action')
                ->label(function ($row) {
                    $next_level_role_id = $row->next_level_role_id;
                    $msg = '';
                    if ($next_level_role_id == Codemaster::getIdByCode(4402)) {
                        $msg = 'VERIFIED BUT APPROVAL PENDING';
                    } elseif ($next_level_role_id == Codemaster::getIdByCode(4403)) {
                        $msg = 'VERIFIED AND APPROVED';
                    }
                    $canEdit = false;
                    if (
                        (CheckAuthHelper::isCommmonVerifier() && $next_level_role_id == Codemaster::getIdByCode(4401)) ||
                        (CheckAuthHelper::isCommonApprover() && $next_level_role_id == Codemaster::getIdByCode(4402))
                    ) {
                        $canEdit = true;
                    }
                    if (! $canEdit) {
                        return $msg;
                    }
                    $link = route('backfromjbactions').'?id='.Crypt::encryptString($row->beneficiary->application_id);

                    return view('coulmn_button.actions', [
                        'link' => $link,
                        'tooltip' => 'Edit',
                    ])->render();
                })
                ->html(),
        ];
    }

    public function builder(): Builder
    {
        $query = BackFromJb::with([
            'beneficiary.contact',
        ])->whereHas('beneficiary', function ($q) {
            foreach ($this->filter_condition as $col => $val) {
                $q->where($col, $val);
            }
        });
        if (! empty($this->next_level_role_id)) {
            $query->where('next_level_role_id', $this->next_level_role_id);
        }
        if ($this->district_id || $this->rural_urban || $this->blockurban || $this->gp_ward || $this->sub_div) {
            $query = EncryptionArray::applyLocationFilters(
                $query,
                $this->district_id,
                $this->rural_urban,
                $this->blockurban,
                $this->gp_ward,
                $this->sub_div
            );
        }

        // dd($query->get());
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
