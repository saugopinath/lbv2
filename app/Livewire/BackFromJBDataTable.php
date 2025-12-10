<?php

namespace App\Livewire;

use App\Models\BeneficiaryCommonList;
use App\Exports\BeneficiariesExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use App\Models\Codemaster;
use App\Models\BackFromJb;
use App\Helpers\CheckAuthHelper;
use App\Helpers\EncryptionArray;

class BackFromJBDataTable extends DataTableComponent
{
    public ?int $perPage = 5;
    protected $listeners = [
        'doSearch' => 'updateFilters',
    ];
    public $district_id, $rural_urban, $blockurban, $gp_ward, $next_level_role_id, $revertrejectAction, $revertrejectCauses, $sub_div;
    public array $filter_condition = [];
    public function mount(): void
    {
        if (CheckAuthHelper::isCommmonVerifier()) {
            $this->next_level_role_id = Codemaster::getIdByCode(4401);
        } elseif (CheckAuthHelper::isCommonApprover()) {
            $this->next_level_role_id = Codemaster::getIdByCode(4402);
        }

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

    public function configure(): void
    {
        $this->setPrimaryKey('sourceable_id')
            ->setPaginationEnabled()
            ->setPerPageAccepted([5, 10])
            ->setPerPage($this->perPage)
            ->setPerPageVisibilityEnabled()
            ->setSearchEnabled()
            ->setSearchLive()
            ->setColumnSelectDisabled()
        ;
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
        $this->perPage = (int)$value;
        $this->setPerPage((int)$value);
        $this->resetPage();
    }
    public function updateFilters($filters)
    {
        $this->district_id = $filters['district_id'];
        $this->rural_urban = $filters['rural_urban'] ?? null;
        $this->blockurban = $filters['blockurban'];
        $this->gp_ward = $filters['gp_ward'];
        $this->sub_div = $filters['subdivision_id'];
        $this->next_level_role_id = $filters['application_type'];
    }

    public function columns(): array
    {
        return [
            Column::make("Application ID", "application_id")
                ->label(fn($row) => $row->beneficiary->sourceable_id ?? 'N/A')
                ->sortable()
                ->searchable(function ($query, $searchTerm) {
                    $query->whereHas('beneficiary', function ($q) use ($searchTerm) {
                        $q->where('sourceable_id', 'ILIKE', "%{$searchTerm}%");
                    });
                }),

            Column::make("Applicant Name", "full_name")
                ->label(fn($row) => $row->beneficiary->sourceable->full_name ?? 'N/A')
                ->searchable(function ($query, $searchTerm) {
                    $query->whereHas('beneficiary.sourceable', function ($q) use ($searchTerm) {
                        $q->where('full_name', 'ILIKE', "%{$searchTerm}%");
                    });
                }),

            Column::make("Mobile No", "Mobile No")
                ->label(fn($row) => $row->beneficiary->sourceable->mobile_no
                    ?? 'N/A'),

            Column::make("Address", "Address")
                ->label(fn($row) => $row->beneficiary->sourceable->contact->getFullAddress() ?? 'N/A')
                ->html(),

            Column::make("Action")
                ->label(function ($row) {
                    $canEdit = false;
                    if (
                        (CheckAuthHelper::isCommmonVerifier() && (Codemaster::getIdByCode(4401))) ||
                        (CheckAuthHelper::isCommonApprover() && (Codemaster::getIdByCode(4402)))
                    ) {
                        $canEdit = true;
                    }
                    if (!$canEdit) {
                        return 'Approved';
                    }
                    $link = route('backfromjbactions') . '?id=' . Crypt::encryptString($row->beneficiary->sourceable->application_id);
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
            'beneficiary.sourceable.contact'
        ])->whereHas('beneficiary', function ($q) {
            foreach ($this->filter_condition as $col => $val) {
                $q->where($col, $val);
            }
        });
        if (!empty($this->next_level_role_id)) {
            $query->where('next_level_role_id', $this->next_level_role_id);
        }
        if ($this->district_id || $this->rural_urban || $this->blockurban || $this->gp_ward || $this->sub_div) {
            $query = EncryptionArray::applyBackFromJB(
                $query,
                $this->district_id,
                $this->rural_urban,
                $this->blockurban,
                $this->gp_ward,
                $this->sub_div
            );
        }
        return $query;
    }
}
