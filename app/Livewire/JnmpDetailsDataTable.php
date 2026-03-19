<?php

namespace App\Livewire;

use App\Models\BeneficiaryPersonalDetail;
use App\Helpers\EncryptionArray;
use App\Exports\BeneficiariesExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Actions\Action;
use Rappasoft\LaravelLivewireTables\Views\Filters\TextFilter;
use App\Models\Codemaster;
use Livewire\Attributes\On;

class JnmpDetailsDataTable extends DataTableComponent
{
    public ?int $perPage = 5;
    public string $reportType;
    public $selectedApplicationId;
    public string $login_type = '';
    public string $search = '';

    public $district_id, $rural_urban, $blockurban, $gp_ward, $next_level_role_id, $revertrejectAction, $revertrejectCauses, $sub_div;
    protected $listeners = ['filtersApplied'];

    public $loginDistrictCode, $loginSubdivisionCode, $loginBlockCode;
    public array $filter_condition = [];
    public function mount(): void
    {
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
    public function filtersApplied($filters)
    {
        // dd($filters);
        $this->district_id = $filters['district_id'];
        $this->rural_urban = $filters['rural_urban'] ?? null;
        $this->blockurban = $filters['blockurban'];
        $this->gp_ward = $filters['gp_ward'];
        $this->sub_div = $filters['subdivision_id'];
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
            ->setBulkActionsEnabled();

        $this->setHideBulkActionsWhenEmptyEnabled();

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
    public function filters(): array
    {
        return [
            TextFilter::make('Application ID')
                ->filter(function ($query, $value) {
                    $query->where('application_id', 'ILIKE', "%{$value}%");
                }),

            TextFilter::make('Applicant Name')
                ->filter(function ($query, $value) {
                    $query->where('beneficiary_name', 'ILIKE', "%{$value}%");
                }),
        ];
    }
    public function columns(): array
    {
        return [

            Column::make("Application ID")
                ->label(fn($row) => $row->application_id ?? 'N/A'),

            Column::make("Beneficiary ID")
                ->label(fn($row) => $row->beneficiary_id ?? 'N/A'),

            Column::make("Applicant Name")
                ->label(fn($row) => $row->beneficiary_name ?? 'N/A'),

            Column::make("Father's Name")
                ->label(function ($row) {
                    return optional(
                        $row->relationships->firstWhere(
                            'relation_type_id',
                            Codemaster::getIdByCode(131)
                        )
                    )->full_name ?? 'N/A';
                }),

            Column::make("Address")
                ->label(fn($row) => $row->contact->getFullAddress() ?? 'N/A')
                ->html(),

            Column::make("Mobile No")
                ->label(fn($row) => $row->mobile_no ?? 'N/A'),

            Column::make("Actions")
                ->label(function ($row) {

                    $id = $row->application_id;

                    return view('coulmn_button.actions', [
                        'wireClick' => "\$dispatch('openReactivateModal', { id: '$id' })",
                        'tooltip' => 'Activate as Alive'
                    ])->render();
                })
                ->html(),
        ];
    }
    public function builder(): Builder
    {
        $query = BeneficiaryPersonalDetail::query()
            ->with(['contact', 'mapping']);

        $query->where('jnmp_marked', 1);

        $query->whereHas('mapping', function ($q) {
            $q->where('payment_suspend', 1);
        });

        if (!empty($this->filter_condition)) {
            $query->where($this->filter_condition);
        }

        if ($this->district_id || $this->rural_urban || $this->blockurban || $this->gp_ward || $this->sub_div) {
            $query = EncryptionArray::applyLocationFilters(
                $query,
                $this->district_id ? (int) $this->district_id : null,
                $this->rural_urban ? (int) $this->rural_urban : null,
                $this->blockurban ? (int) $this->blockurban : null,
                $this->gp_ward ? (int) $this->gp_ward : null,
                $this->sub_div ? (int) $this->sub_div : null
            );
        }

        return $query;
    }

    #[On('openReactivateModal')]
    public function openReactivateModal($id)
    {
        $this->selectedApplicationId = $id;

        // Send ID to modal component
        $this->dispatch('showReactivateModal', id: $id);
    }

    public function exportExcel()
    {
        $data = $this->builder()
            ->get()
            ->map(function ($row) {

                $father = $row->relationships
                    ->where('relation_type_id', Codemaster::getIdByCode(131))
                    ->first();

                return [
                    'application_id' => $row->application_id ?? 'N/A',
                    'full_name' => $row->beneficiary_name ?? 'N/A',
                    'father_name' => $father->full_name ?? 'N/A',
                    'dob' => $row->dob
                        ? \Carbon\Carbon::parse($row->dob)->format('d-m-Y')
                        : 'N/A',

                    'mobile_no' => $row->mobile_no ?? 'N/A',
                ];
            });

        return Excel::download(new BeneficiariesExport($data), 'jnmp_beneficiaries_all.xlsx');
    }
}
