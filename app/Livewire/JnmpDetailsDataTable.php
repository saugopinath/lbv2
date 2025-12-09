<?php

namespace App\Livewire;

use App\Models\BeneficiaryCommonList;
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
            $this->filter_condition['district_id'] = Crypt::decryptString($select_lgd['district_id']);
        }

        if (!empty($select_lgd['block_id'])) {
            $this->filter_condition['block_id'] = Crypt::decryptString($select_lgd['block_id']);
        }

        if (!empty($select_lgd['subdivision_id'])) {
            $this->filter_condition['sub_division_id'] = Crypt::decryptString($select_lgd['subdivision_id']);
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
        $this->setPrimaryKey('sourceable_id')
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
        $this->perPage = (int)$value;
        $this->setPerPage((int)$value);
        $this->resetPage();
    }
    public function filters(): array
    {
        return [
            TextFilter::make('Application ID')
                ->filter(function ($query, $value) {
                    $query->whereHas('sourceable', function ($q) use ($value) {
                        $q->where('application_id', 'ILIKE', "%{$value}%");
                    });
                }),

            TextFilter::make('Applicant Name')
                ->filter(function ($query, $value) {
                    $query->whereHas('sourceable', function ($q) use ($value) {
                        $q->where('full_name', 'ILIKE', "%{$value}%");
                    });
                }),
        ];
    }
    public function columns(): array
    {
        return [

            Column::make("Application ID")
                ->label(fn($row) => $row->sourceable->application_id ?? 'N/A'),


            Column::make("Beneficiary ID", "beneficiary_id")
                ->label(fn($row) => $row->sourceable->beneficiary_id ?? 'N/A'),

            Column::make("Applicant Name", "full_name")
                ->label(fn($row) => $row->sourceable->full_name ?? 'N/A'),

            Column::make("Father's Name", "fullname")
                ->label(function ($row) {
                    return optional(
                        $row->sourceable->relationships->firstWhere(
                            'relation_type_id',
                            Codemaster::getIdByCode(131)
                        )
                    )->full_name ?? 'N/A';
                }),

            Column::make("Mobile No", "mobile_no")
                ->label(fn($row) => $row->sourceable->mobile_no ?? 'N/A'),

            $columns[] = Column::make("Actions")
                ->label(function ($row) {

                    $id = $row->sourceable->application_id;

                    return view('coulmn_button.actions', [
                        'wireClick' => "\$dispatch('openReactivateModal', { id: '$id' })",
                        'tooltip'   => 'Activate as Alive'
                    ])->render();
                })
                ->html(),

        ];
    }

    public function builder(): Builder
    {
        $query = BeneficiaryCommonList::query()
            ->with([
                'sourceable',
                'sourceable.contact',
                'sourceable.relationships',
                'sourceable.mapping'
            ]);

        // JNMP Marked = 1
        $query->whereHas('sourceable', function ($q) {
            $q->where('jnmp_marked', 1);
        });

        // payment_suspend = 1
        $query->whereHas('sourceable.mapping', function ($q) {
            $q->where('payment_suspend', 1);
        });

        // District Filter
        // if (!empty($this->district_id)) {
        //     $query->whereHas('personal', function ($q) {
        //         $q->where('created_by_dist_code', $this->district_id);
        //     });
        // }

        // // Block / ULB Filter
        // if (!empty($this->blockurban)) {
        //     $query->whereHas('personal', function ($q) {
        //         $q->where('created_by_local_body_code', $this->blockurban);
        //     });
        // }

        // // GP/Ward
        // if (!empty($this->gp_ward)) {
        //     $query->whereHas('contact', function ($q) {
        //         $q->where('gp_ward_code', $this->gp_ward);
        //     });
        // }

        // // Subdivision
        // if (!empty($this->sub_div)) {
        //     $query->whereHas('personal', function ($q) {
        //         $q->where('created_by_subdivision_code', $this->sub_div);
        //     });
        // }

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

                $father = $row->sourceable->relationships
                    ->where('relation_type_id', Codemaster::getIdByCode(131))
                    ->first();

                return [
                    'application_id' => $row->sourceable->application_id ?? 'N/A',
                    'full_name'      => $row->sourceable->full_name ?? 'N/A',
                    'father_name'    => $father->full_name ?? 'N/A',
                    'dob' => $row->sourceable->dob
                        ? \Carbon\Carbon::parse($row->sourceable->dob)->format('d-m-Y')
                        : 'N/A',

                    'mobile_no'      => $row->sourceable->mobile_no ?? 'N/A',
                ];
            });

        return Excel::download(new BeneficiariesExport($data), 'jnmp_beneficiaries_all.xlsx');
    }
}
