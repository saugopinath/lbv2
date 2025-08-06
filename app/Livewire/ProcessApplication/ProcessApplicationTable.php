<?php

namespace App\Livewire\ProcessApplication;


use App\Models\DraftBeneficiaryPersonal;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Carbon\Carbon;

class ProcessApplicationTable extends DataTableComponent
{
    protected $model = DraftBeneficiaryPersonal::class;
   public $panchayat_code = null;



    protected $listeners = ['gpSelected'];



    public function gpSelected($data)
    {
        $this->panchayat_code = $data['gp_code'] ?? null;

    }

    public function builder(): Builder
    {
         $query = DraftBeneficiaryPersonal::query()
        ->with(
            'ben_relationships',
            'contacts',
            'contacts.panchayat',
            'contacts.municipality'
        )
        ->select('id', 'application_id', 'full_name', 'dob', 'sub_division_id', 'block_id');



     if ($this->panchayat_code) {
            $query->whereHas('contacts.panchayat', function ($q) {
                $q->where('id', $this->panchayat_code);
            });
        }

    return $query;

    }

    public function configure(): void
    {
        $this->setPrimaryKey('id');
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
                    if (!$row->dob) return '-';
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



            Column::make('Action')
                ->label(
                    fn($row) =>
                    view('components.datatable-action', ['row' => $row])->render()
                )->html()
        ];
    }


}