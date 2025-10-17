<?php

namespace App\Livewire;

use App\Models\CasteModificationInfo;
use App\Models\Codemaster;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Crypt;
use Rappasoft\LaravelLivewireTables\DataTableComponent;

class CasteModificationListTable extends DataTableComponent
{
    protected $listeners = [
        'refreshDatatable' => '$refresh',
        'filtersApplied'   => 'setFilters',
        'resetFilters'     => 'resetFilters',
        'resettable'  => 'resettable',
    ];
    public int $rowNumberOffset = 0;
    // public int $roleId = 0;
    // public array $filter_condition = [];
    // public string $applicantStatus = '';
    // public string $casteId = '';
    // public bool $showTable = false;   // ✅ control table visibility
    // public bool $showActions = false;
    // public int $action_visible = 0;
    // public int $nextLevelRequestId = 0;
    public string $applicantStatus = '';
    public string $casteId = '';
    public int $roleId = 0;
    public bool $showTable = false;
    public int $action_visible = 0;
    public int $nextLevelRequestId = 0;
    public array $filter_condition = [];

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->rowNumberOffset = ($this->getPage() - 1) * $this->getPerPage();

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
        $this->setLoadingPlaceholderEnabled();
    }
    public function mount($applicantStatus = '', $casteId = '')
    {
        $this->applicantStatus = $applicantStatus;
        $this->casteId = $casteId;

        $select_lgd = session('lgd_session') ?? [];

        if (!empty($select_lgd['district_id'])) {
            $this->filter_condition['district_id'] = Crypt::decryptString($select_lgd['district_id']);
        }
        if (!empty($select_lgd['block_id'])) {
            $this->filter_condition['block_id'] = Crypt::decryptString($select_lgd['block_id']);
        }
        if (!empty($select_lgd['subdivision_id'])) {
            $this->filter_condition['subdivision_id'] = Crypt::decryptString($select_lgd['subdivision_id']);
        }
        if (!empty($select_lgd['role_id'])) {
            $this->roleId = (int) Crypt::decryptString($select_lgd['role_id']);
        }

        // Show actions based on status and role
        $this->showActions();
    }

    protected function showActions(): void
    {
        $this->action_visible = ($this->applicantStatus === 'PL' && auth()->user()?->hasAnyRole(['Verifier', 'Approver'])) ? 1 : 0;
    }

    // public function mount($applicantStatus = ''): void
    // {
    //     $this->applicantStatus = $applicantStatus;
    //     $select_lgd = session('lgd_session');

    //     if (!empty($select_lgd['district_id'])) {
    //         $this->filter_condition['district_id'] = Crypt::decryptString($select_lgd['district_id']);
    //     }

    //     if (!empty($select_lgd['block_id'])) {
    //         $this->filter_condition['block_id'] = Crypt::decryptString($select_lgd['block_id']);
    //     }

    //     if (!empty($select_lgd['subdivision_id'])) {
    //         $this->filter_condition['subdivision_id'] = Crypt::decryptString($select_lgd['subdivision_id']);
    //     }

    //     if (!empty($select_lgd['role_id'])) {
    //         $this->roleId = (int) Crypt::decryptString($select_lgd['role_id']);
    //     }
    // }
    public function setFilters($filters): void
    {
        // dd('bhbhbjhb');
        // $this->dispatch('hideLoader');
        $this->applicantStatus = $filters['status'] ?? '';
        $this->casteId         = $filters['caste'] ?? '';
        $this->action_visible = ($this->applicantStatus == 'PL') ? 1 : 0;
        if (!empty($this->applicantStatus)) {
            $this->showTable = true;
        } else {
            $this->showTable = false;
        }
        // dump($this->applicantStatus);
        // dump($this->casteId);
        // dd($this->action_visible);
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->applicantStatus   = '';
        $this->casteId           = '';
        $this->nextLevelRequestId = 0;
        $this->showTable = false;
        $this->resetPage();
    }
    public function resettable(): void
    {
        $this->showTable = false;
        $this->resetPage();
    }


    public function builder(): Builder
    {
        $query = CasteModificationInfo::query()
            ->with([
                'beneficiaryCommonList.sourceable',
                'beneficiaryCommonList.sourceable.contact',
                'beneficiaryCommonList.sourceable.relationships',
            ])
            ->whereHas('beneficiaryCommonList', function ($q) {
                foreach ($this->filter_condition as $col => $val) {
                    $q->where($col, $val);
                }
            });
            // if (!empty($this->filter_condition)) {
            //     $query->where($this->filter_condition);
            // }
        if (!empty($this->applicantStatus)) {
            if ($this->applicantStatus == 'PL') {
                if (in_array($this->roleId, [4, 5])) {
                    $this->nextLevelRequestId = Codemaster::getIdByCode(2202);
                    $this->action_visible = 1;
                } elseif (in_array($this->roleId, [6, 7])) {
                    $this->nextLevelRequestId = Codemaster::getIdByCode(2201);
                    $this->action_visible = 1;
                }
            } elseif ($this->applicantStatus == 'APL') {
                $this->nextLevelRequestId = Codemaster::getIdByCode(2202);
            } elseif ($this->applicantStatus == 'VPL') {
                $this->nextLevelRequestId = Codemaster::getIdByCode(2201);
            } elseif ($this->applicantStatus == 'VL') {
                $this->nextLevelRequestId = Codemaster::getIdByCode(2202);
            } elseif ($this->applicantStatus == 'AL') {
                $this->nextLevelRequestId = Codemaster::getIdByCode(2203);
            } elseif ($this->applicantStatus == 'RL') {
                $this->nextLevelRequestId = Codemaster::getIdByCode(2204);
            } else {
                $this->nextLevelRequestId = 0;
            }
            $query->where('next_level_requested_id', $this->nextLevelRequestId);
        }
        return $query;
    }
    public function columns(): array
    {
        return [
            Column::make("ID", "id"),
            Column::make("Application Id", "application_id"),
            Column::make("Name")
                ->label(fn($row) => $row->beneficiaryCommonList?->sourceable?->full_name ?? 'N/A'),
            Column::make("Father's Name")
                ->label(fn($row) => $row->beneficiaryCommonList?->sourceable?->relationships
                    ->where('relation_type_id', 79)->first()?->full_name ?? 'N/A'),
            //     Column::make("Actions")
            //         ->label(fn($row) => view('coulmn_button.view', [
            //             'link' => route('view-beneficiary-details', [
            //                 'application_id' => Crypt::encrypt($row->application_id)
            //             ]),
            //             'tooltip' => 'view Application',
            //         ])->render())
            //         ->html()
            //         ->hideIf(
            //             !auth()->user()?->hasAnyRole(['Verifier', 'Approver'])
            //         ),
            Column::make("Actions")
                ->label(function ($row) {
                    if (auth()->user()?->hasAnyRole(['Verifier', 'Approver'])) {
                        return view('coulmn_button.view', [
                            'link' => route('view-beneficiary-details', [
                                'application_id' => Crypt::encrypt($row->application_id)
                            ]),
                            'tooltip' => 'View Application',
                        ])->render();
                    }

                    if (auth()->user()?->hasRole('Operator')) {
                        return view('coulmn_button.view', [
                            'link' => route('caste-modification.edit', [
                                'application_id' => Crypt::encryptstring($row->application_id),
                                'beneficiary_id'=> Crypt::encryptstring($row->beneficiary_id)
                            ]),
                            'tooltip' => 'Edit Application',
                        ])->render();
                    }

                    return ''; 
                })
                ->html(),

        ];
    }

    public function render(): \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        if (!$this->showTable) {
            return view('livewire.caste-modification-list-table');
        }

        return parent::render();
    }
}
