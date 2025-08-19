<?php

namespace App\Livewire\ProcessApplication;


use Carbon\Carbon;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Crypt;
use App\Models\DraftBeneficiaryPersonal;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;

class ProcessApplicationTable extends DataTableComponent
{
    protected $model = DraftBeneficiaryPersonal::class;
    public $panchayat_code = null;

    public ?int $perPage = 5;







    public function builder(): Builder
    {
        $lgd_session = session('lgd_session', []);
        $blockId = isset($lgd_session['block_id']) ? Crypt::decryptString($lgd_session['block_id']) : null;
        $districtId = isset($lgd_session['district_id']) ? Crypt::decryptString($lgd_session['district_id']) : null;
        $subDivisionId = isset($lgd_session['subdivision_id']) ? Crypt::decryptString($lgd_session['subdivision_id']) : null;


        $query = DraftBeneficiaryPersonal::query()
            ->with(
                'ben_relationships',
                'contacts',
                'contacts.panchayat',
                'contacts.municipality'
            )
            ->select('id', 'application_id', 'full_name', 'dob', 'sub_division_id', 'block_id');

        if ($blockId) {
            $query->where('block_id', $blockId);
        } elseif ($subDivisionId) {
            $query->where('sub_division_id', $subDivisionId);
        } elseif ($districtId) {
            $query->where('district_id', $districtId);

        }

        // dd($query->toSql(), $query->getBindings());


        return $query;

    }

    public function configure(): void
    {
        $this->setPrimaryKey('id')
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
        $this->perPage = (int) $value;
        $this->setPerPage((int) $value);
        $this->resetPage();
    }
    public function columns(): array
    {

        return [
            Column::make('ID', 'id')
                ->sortable()
                ->searchable()
                ->hideIf(true),

            Column::make('Application ID', 'application_id')
                ->sortable()
                ->searchable(),

            Column::make('Applicant Name', 'full_name')
                ->sortable()
                ->searchable(),

            Column::make('Father\'s Name')
                ->label(
                    fn($row) =>
                    $row->ben_relationships->where('relation_type_id', 79)->first()?->full_name ?? '-'
                ),

            Column::make('Age')
                ->label(function ($row) {
                    if (!$row->dob)
                        return '-';
                    $dob = Carbon::parse($row->dob);
                    return $dob->diff(Carbon::now())->format('%y ');
                }),


            Column::make('GP / Municipality')
                ->label(function ($row) {
                    if ($row->contacts?->panchayat?->name) {
                        return 'GP: ' . $row->contacts->panchayat->name;
                    } elseif ($row->contacts?->municipality?->name) {
                        return 'Municipality: ' . $row->contacts->municipality->name;
                    } else {
                        return '-';
                    }
                }),



            // Column::make('Action')
            //     ->label(
            //         fn($row) =>
            //         view('components.datatable-action', ['row' => $row])->render()
            //     )->html()
        ];
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.table.table', [
            'rows' => $this->getRows(),

        ]);
    }


}
