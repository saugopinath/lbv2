<?php

namespace App\Livewire;

use App\Models\BeneficiaryPersonalDetail;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use App\Models\Codemaster;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\On;
use App\Models\DsPhase;

class DuplicateApplicantDataTable extends DataTableComponent
{
    public ?int $perPage = 5;
    public $key,$value;
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
    // #[On('dsMarked')]
    // public function dsMarked()
    // {
    //     $this->dispatch('refreshDatatable');
    // }
    public function columns(): array
    {
        return [
            Column::make("Application ID", "application_id")
                ->label(fn($row) => $row->application_id ?? 'N/A')
                ->sortable()
                ->searchable(function ($query, $searchTerm) {
                    $query->whereHas('sourceable', function ($q) use ($searchTerm) {
                        $q->where('application_id', 'ILIKE', "%{$searchTerm}%");
                    });
                }),

            Column::make("Applicant Name", "full_name")
                ->label(fn($row) => $row->beneficiary_name ?? 'N/A'),

            Column::make("Father's Name", "ben_father_name")
                ->label(fn($row) => $row->ben_father_name ?? 'N/A'),

            Column::make("Mobile No", "Mobile No")
                ->label(fn($row) => $row->other_details['mobile_no']
                    ?? 'N/A'),

            // Column::make("Address", "Address")
            //     ->label(fn($row) => $row->contact->getFullAddress() ?? 'N/A')
            //     ->html(),

            // Column::make("Status", "Status")
            //     ->label(fn($row) => $row->getStatusText()
            //         ?? 'N/A'),

            $columns[] = Column::make("Actions")
                ->label(function ($row) {
                    if ($row->application_type == Codemaster::getIdByCode(41) || $row->ds_phase != DsPhase::where('is_current', true)->value('phase_code')) {
                        $id = $row->application_id;
                        return view('coulmn_button.actions', [
                            'wireClick' => "\$dispatch('opendsMarkModal', { id: '$id' })",
                            'tooltip' => 'Ds Mark'
                        ])->render();
                    } else {
                        return 'Already Marked';
                    }
                })
                ->html(),
        ];
    }
    // #[On('dsMark')]
    // public function dsMark($id = null)
    // {
    //     // dd($id);
    //     $benData = BeneficiaryCommonList::find($id);
    //     dd($benData);
    // }
#[On('aadhaarCheckedds')]
public function dsMark($benData)
{
    $this->key = key($benData);
    $this->value = current($benData);
}

public function builder(): Builder
{
    $query = BeneficiaryPersonalDetail::with(['contact', 'aadhaar']);
    if (!empty($this->value)) {
        $query->whereHas('aadhaar', function ($q) {
            $q->where('aadhar_hash', $this->value);
        });
    }

    return $query;
}

    // public function builder(): Builder
    // {
    //     $query = BeneficiaryPersonalDetail::with('contact');
    //     $filters = [
    //         'dup_aadhaar' => 'encoded_aadhar',
    //         'dup_bank'    => 'bank_account_number',
    //         'dup_mobile'  => 'mobile_no',
    //         'dup_name'    => 'full_name',
    //     ];
    //     $sessionUsed = false;
    //     foreach ($filters as $sessionKey => $column) {
    //         if (Session::has($sessionKey)) {
    //             $value = Session::get($sessionKey);
    //             if (!empty($value)) {
    //                 $query->where($column, $value);
    //                 $sessionUsed = true;
    //             }
    //         }
    //     }
    //     if ($sessionUsed) {
    //         foreach (array_keys($filters) as $key) {
    //             Session::forget($key);
    //         }
    //     }
    //     return $query;
    // }
}
