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
        $select_lgd = session('lgd_session');
        if (!empty($select_lgd['district_id'])) {
            $this->filter_condition['lb_dist_code'] = Crypt::decryptString($select_lgd['district_id']);
        }
        if (!empty($select_lgd['block_id'])) {
            $this->filter_condition['lb_local_body_code'] = Crypt::decryptString($select_lgd['block_id']);
        }
        if (!empty($select_lgd['subdivision_id'])) {
            $this->filter_condition['lb_local_body_code'] = Crypt::decryptString($select_lgd['subdivision_id']);
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
        // dd($filters);
        $this->district_id = $filters['district_id'];
        $this->rural_urban = $filters['rural_urban'] ?? null;
        $this->blockurban = $filters['blockurban'];
        $this->gp_ward = $filters['gp_ward'];
        $this->sub_div = $filters['subdivision_id'];
    }

    public function columns(): array
    {
        return [
            Column::make("Application ID", "application_id")
                ->label(fn($row) => $row->beneficiary->sourceable->application_id ?? 'N/A')
                ->sortable()
                ->searchable(function ($query, $searchTerm) {
                    $query->whereHas('sourceable', function ($q) use ($searchTerm) {
                        $q->where('application_id', 'ILIKE', "%{$searchTerm}%");
                    });
                }),

            Column::make("Applicant Name", "full_name")
                ->label(fn($row) => $row->beneficiary->sourceable->full_name ?? 'N/A'),

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
                    $link = route('#') . '?id=' . Crypt::encryptString($row->beneficiary->sourceable->application_id);
                    return view('coulmn_button.actions1', [
                        'link' => $link,
                        'tooltip' => 'Edit',
                    ])->render();
                })
                ->html(),
        ];
    }

    // public function builder(): Builder
    // {
    //     // $val = BackFromJb::with('beneficiary.sourceable')->get();
    //     // dd($val);
    //     $query = BackFromJb::with('beneficiary.sourceable.contact');
    //     if ($this->district_id || $this->rural_urban || $this->blockurban || $this->gp_ward || $this->sub_div) {
    //         $query = EncryptionArray::applyLocationFilters(
    //             $query,
    //             $this->district_id ? (int) $this->district_id : null,
    //             $this->rural_urban ? (int) $this->rural_urban : null,
    //             $this->blockurban ? (int) $this->blockurban : null,
    //             $this->gp_ward ? (int) $this->gp_ward : null,
    //             $this->sub_div ? (int) $this->sub_div : null
    //         );
    //     }

    //     return $query;
    // }
    public function builder(): Builder
{
    $query = BackFromJb::with('beneficiary.sourceable.contact');

    // FILTERS
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

    // SEARCH
    // if (!empty($this->search)) {
    //     $query->whereHas('beneficiary.sourceable.contact', function ($q) {
    //         $q->where('mobile_no', 'like', "%{$this->search}%")
    //           ->orWhere('name', 'like', "%{$this->search}%")
    //           ->orWhere('address', 'like', "%{$this->search}%");
    //     });
    // }

    return $query;
}

}
