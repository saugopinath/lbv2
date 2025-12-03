<?php

namespace App\Livewire;

use App\Models\BeneficiaryCommonList;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use App\Models\Codemaster;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;

class DuplicateApplicantDataTable extends DataTableComponent
{
    public ?int $perPage = 5;
    public function mount(): void {}
    public function configure(): void
    {
        $this->setPrimaryKey('sourceable_id')
            ->setPaginationEnabled()
            ->setPerPageAccepted([5, 10])
            ->setPerPage($this->perPage)
            ->setSearchDisabled()
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



    public function updatedPerPage($value): void
    {
        $this->perPage = (int)$value;
        $this->setPerPage((int)$value);
        $this->resetPage();
    }

    public function columns(): array
    {
        return [
            Column::make("Application ID", "application_id")
                ->label(fn($row) => $row->sourceable->application_id ?? 'N/A')
                ->sortable()
                ->searchable(function ($query, $searchTerm) {
                    $query->whereHas('sourceable', function ($q) use ($searchTerm) {
                        $q->where('application_id', 'ILIKE', "%{$searchTerm}%");
                    });
                }),

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

            Column::make("Mobile No", "Mobile No")
                ->label(fn($row) => $row->sourceable->mobile_no
                    ?? 'N/A'),

            Column::make("Address", "Address")
                ->label(fn($row) => $row->sourceable->contact->getFullAddress() ?? 'N/A')
                ->html(),

            Column::make("Status", "Status")
                ->label(fn($row) => $row->sourceable->getStatusText()
                    ?? 'N/A'),

            $columns[] = Column::make("Actions")
                ->label(function ($row) {
                    $id = $row->sourceable->application_id;
                    return view('coulmn_button.actions', [
                        'wireClick' => "\$dispatch('opendsMarkModal', { id: '$id' })",
                        'tooltip' => 'Ds Mark'
                    ])->render();
                })
                ->html(),
        ];
    }

    public function builder(): Builder
    {
        // session()->flush();
        // Session::forget('dup_aadhaar');
        $value = '';
        $key = '';
        if (Session::get('dup_aadhaar')) {
            $value = Session::get('dup_aadhaar');
            $key = 'encoded_aadhar';
        }
        // dd($key, $value);
        // Session::forget('dup_aadhaar');
        $query = BeneficiaryCommonList::with('sourceable.relationships', 'sourceable.contact');
        $query->where($key, $value);
        return $query;
    }
}
