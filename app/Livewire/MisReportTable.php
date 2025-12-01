<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Codemaster;
use App\Helpers\EncryptionArray;
use App\Models\BenRejectDetails;
use App\Models\BeneficiaryPersonal;
use App\Models\BeneficiaryCommonList;
use Illuminate\Support\Facades\Crypt;
use App\Models\DraftBeneficiaryPersonal;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BeneficiariesExport;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class MisReportTable extends DataTableComponent
{
    public ?int $perPage = 5;
    public string $reportType;
    public string $login_type = '';
    public string $search = '';

    public $district_id, $rural_urban, $blockurban, $gp_ward;
    protected $listeners = ['filtersApplied'];

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
        $this->district_id = $filters['district_id'];
        $this->rural_urban = $filters['rural_urban'] ?? null;
        $this->blockurban = $filters['blockurban'];
        $this->gp_ward = $filters['gp_ward'];
    }

    public function configure(): void
    {
        $this->setPrimaryKey('sourceable_id')
            ->setPaginationEnabled()
            ->setPerPageAccepted([5, 10])
            ->setPerPage($this->perPage)
            ->setPerPageVisibilityEnabled()
            ->setSearchdisabled()
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

    public function columns(): array
    {
        $counts = $this->getCount();

        return [
            Column::make('Total Verified')
                ->label(
                    fn() => '<span class="font-semibold text-green-700">'
                        . $counts['total_verified'] .
                        '</span>'
                )
                ->html(),

            Column::make('Total Approved')
                ->label(
                    fn() => '<span class="font-semibold text-blue-700">'
                        . $counts['total_approved'] .
                        '</span>'
                )
                ->html(),
        ];
    }

    public function getCount()
    {
        $entryVerified = Codemaster::getIdByCode(23);
        $entryApproved = Codemaster::getIdByCode(0);

        $filter = $this->filter_condition;

        // Total Verified (DraftBeneficiaryPersonal)
        $total_verified = BeneficiaryCommonList::whereHasMorph(
            'sourceable',
            DraftBeneficiaryPersonal::class,
            fn($q) => $q->where('next_level_role_id', $entryVerified)
        )
            ->when(!empty($filter), fn($q) => $q->where($filter))
            ->count();

        // Total Approved (BeneficiaryPersonal)
        $total_approved = BeneficiaryCommonList::whereHasMorph(
            'sourceable',
            BeneficiaryPersonal::class,
            fn($q) => $q->where('next_level_role_id', $entryApproved)
        )
            ->when(!empty($filter), fn($q) => $q->where($filter))
            ->count();

        return [
            'total_verified' => $total_verified,
            'total_approved' => $total_approved,
        ];
    }


    /** MAIN QUERY */
    public function builder(): Builder
    {

        $query = BeneficiaryCommonList::with([
            'sourceable',
            'sourceable.contact',
            'sourceable.relationships'
        ]);

        return $query;
    }
}
