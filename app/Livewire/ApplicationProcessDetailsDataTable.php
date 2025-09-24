<?php

namespace App\Livewire;

use App\Models\BeneficiaryCommonList;
use App\Helpers\EncryptionArray;
use App\Exports\BeneficiariesExport;
use App\Models\BeneficiaryPersonal;
use App\Models\FaultyBeneficiaryPersonal;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Actions\Action;
use Rappasoft\LaravelLivewireTables\Views\Filters\TextFilter;
use App\Models\Codemaster;
use Carbon\Carbon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use App\Models\DraftBeneficiaryPersonal;
use App\Models\AcceptRejectInfo;
use App\Models\BeneficiaryAadhaar;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;
use App\Models\BenRejectDetails;
use App\Models\DraftBeneficiaryBank;
use App\Models\DraftBeneficiaryContact;
use App\Models\DraftBeneficiaryDeclaration;
use App\Models\DraftBeneficiaryRelationship;

class ApplicationProcessDetailsDataTable extends DataTableComponent
{
    public ?int $perPage = 5;
    public string $reportType;
    public string $login_type = '';
    public string $search = '';

    public $district_id, $rural_urban, $blockurban, $gp_ward, $next_level_role_id, $revertrejectAction, $revertrejectCauses;
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
            $this->filter_condition['subdivision_id'] = Crypt::decryptString($select_lgd['subdivision_id']);
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
    public function bulkActions(): array
    {
        $user = auth()->user();
        $actions = [
            'exportSelected' => 'Export',
        ];
        if ($user->hasAnyRole(['Approver', 'Delegated Approver'])) {
            $actions['bulkapprove'] = 'Approve';
        }
        if ($user->hasAnyRole(['Verifier', 'Delegated Verifier'])) {
            $actions['bulkverify'] = 'Verify';
        }
        if ($user->hasAnyRole(['Approver', 'Delegated Approver', 'Verifier', 'Delegated Verifier'])) {
            $actions['bulkreject'] = 'Reject';
            $actions['bulkrevert'] = 'Revert';
        }
        return $actions;
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

            Column::make("Age", "age")
                ->label(fn($row) => Carbon::parse($row->sourceable->dob)->age
                    ?? 'N/A'),
            Column::make("Actions")
                ->label(function ($row) {
                    $url = route('draft-application.view', $row->sourceable->application_id);

                    return new HtmlString(
                        '<button type="button" onclick="window.open(\'' . $url . '\', \'_blank\')" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">View</button>'
                    );
                }),
        ];
    }
    public function builder(): Builder
    {
        $query = BeneficiaryCommonList::with(['sourceable.relationships']);
        if ($this->district_id || $this->rural_urban || $this->blockurban || $this->gp_ward) {
            $query = EncryptionArray::applyLocationFilters(
                $query,
                $this->district_id ? (int) $this->district_id : null,
                $this->rural_urban ? (int) $this->rural_urban : null,
                $this->blockurban ? (int) $this->blockurban : null,
                $this->gp_ward ? (int) $this->gp_ward : null
            );
        }
        $user = auth()->user();
        $next_level_role_id = null;

        if ($user->hasAnyRole(['Approver', 'Delegated Approver'])) {
            $next_level_role_id = Codemaster::getIdByCode(23);
        }
        if ($user->hasAnyRole(['Verifier', 'Delegated Verifier'])) {
            $next_level_role_id = Codemaster::getIdByCode(22);
        }
        if ($user->hasRole('Operator')) {
            $next_level_role_id = Codemaster::getIdByCode(21);
        }
        if ($next_level_role_id) {
            $query->whereHas(
                'sourceable',
                function ($q) use ($next_level_role_id) {
                    $q->where('next_level_role_id', $next_level_role_id);
                }
            );
        }
        return $query;
    }

    public function bulkverify()
    {
        $ids = $this->getSelected();
        $approverRoleId = Codemaster::getIdByCode(23);
        $select_lgd = session('lgd_session');
        $user_id = Crypt::decryptString($select_lgd['role_id']);
        foreach ($ids as $id) {
            DraftBeneficiaryPersonal::where('application_id', $id)
                ->update(['next_level_role_id' => $approverRoleId]);
            $beneficiary = DraftBeneficiaryPersonal::where('application_id', $id)->first();
            AcceptRejectInfo::Create(
                [
                    'application_id' => $beneficiary->application_id,
                    'beneficiary_id' => $beneficiary->application_id,
                    'ip_address'     => request()->ip(),
                    'user_id'        => $user_id,
                    'browser'        => request()->header('User-Agent'),
                    'model_name'     => null,
                    'op_type'        => Codemaster::getIdByCode(2302),
                    'revert_reason_cause_id' => null,
                    'revert_reason_remarks'  => null,
                    'parent_id'      => AcceptRejectInfo::where('application_id', $id)
                        ->latest('id')
                        ->value('id') ?? null,
                ]
            );
        }

        $this->clearSelected();
    }


    public function bulkapprove()
    {
        $ids = $this->getSelected();
        // $drafts = DraftBeneficiaryPersonal::whereIn('application_id', $ids)->get();
        // foreach ($drafts as $draft) {
        //     $draft->delete();
        // }

        foreach ($ids as $id) {
            $draft = DraftBeneficiaryPersonal::where('application_id', $id)->first();
            if ($draft) {
                $draft->delete();
            }
            $select_lgd = session('lgd_session');
            AcceptRejectInfo::Create(
                [
                    'application_id' => $draft->application_id,
                    'beneficiary_id' => $draft->application_id,
                    'ip_address'     => request()->ip(),
                    'user_id'        => Crypt::decryptString($select_lgd['role_id']),
                    'browser'        => request()->header('User-Agent'),
                    'model_name'     => null,
                    'op_type'        => Codemaster::getIdByCode(2303),
                    'revert_reason_cause_id' => null,
                    'revert_reason_remarks'  => null,
                    'parent_id'      => AcceptRejectInfo::where('application_id', $draft->application_id)
                        ->latest('id')
                        ->value('id') ?? null,
                ]
            );
        }

        $this->clearSelected();
    }
    public function bulkrevert()
    {
        $this->handleBulkAction('revert');
    }

    public function bulkreject()
    {
        $this->handleBulkAction('reject');
    }
    public function handleBulkAction($action)
    {
        $this->revertrejectCauses = Codemaster::where('code', 12)->first()->children()->get();
        $this->revertrejectAction = $action;
        $this->dispatch('open-bulk-revert-modal', action: $action, revertrejectCauses: $this->revertrejectCauses);
    }
    #[On('confirm-bulk-revert')]
    public function confirmBulkRevert($validated)
    {
        $ids = $this->getSelected();
        $select_lgd = session('lgd_session');
        $user_id = Crypt::decryptString($select_lgd['role_id']);
        if ($this->revertrejectAction === 'revert') {
            $user = auth()->user();
            if ($user->hasAnyRole(['Approver', 'Delegated Approver'])) {
                $next_level_role_id = Codemaster::getIdByCode(22);
            }
            if ($user->hasAnyRole(['Verifier', 'Delegated Verifier'])) {
                $next_level_role_id = Codemaster::getIdByCode(21);
            }
            foreach ($ids as $id) {
                DraftBeneficiaryPersonal::where('application_id', $id)
                    ->update(['next_level_role_id' => $next_level_role_id]);
                $beneficiary = DraftBeneficiaryPersonal::where('application_id', $id)->first();
                AcceptRejectInfo::Create(
                    [
                        'application_id' => $beneficiary->application_id,
                        'beneficiary_id' => $beneficiary->application_id,
                        'ip_address'     => request()->ip(),
                        'user_id'        => $user_id,
                        'browser'        => request()->header('User-Agent'),
                        'model_name'     => null,
                        'op_type'        => Codemaster::getIdByCode(2304),
                        'revert_reason_cause_id' => $validated['cause'],
                        'revert_reason_remarks'  => $validated['remark'],
                        'parent_id'      => AcceptRejectInfo::where('application_id', $id)
                            ->latest('id')
                            ->value('id') ?? null,
                    ]
                );
            }
            $this->clearSelected();
        } elseif ($this->revertrejectAction === 'reject') {
            $ids = $this->getSelected();
            foreach ($ids as $id) {
                $benrej = new BenRejectDetails;
                $benrej->application_id     = $id;
                $benrej->created_by     = $user_id;
                $benrej->district_id     = Crypt::decryptString($select_lgd['district_id']);
                $benrej->personal_details     = DraftBeneficiaryPersonal::where('application_id', $id)->get()->toArray();
                $benrej->contact_details      = DraftBeneficiaryContact::where('application_id', $id)->get()->toArray();
                $benrej->bank_details         = DraftBeneficiaryBank::where('application_id', $id)->get()->toArray();
                $benrej->declaration_details  = DraftBeneficiaryDeclaration::where('application_id', $id)->get()->toArray();
                $benrej->relationship_details = DraftBeneficiaryRelationship::where('application_id', $id)->get()->toArray();
                $benrej->aadhar_details       = BeneficiaryAadhaar::where('application_id', $id)->get()->toArray();
                $benrej->save();
                AcceptRejectInfo::Create(
                    [
                        'application_id' => $id,
                        'beneficiary_id' => $id,
                        'ip_address'     => request()->ip(),
                        'user_id'        => $user_id,
                        'browser'        => request()->header('User-Agent'),
                        'model_name'     => null,
                        'op_type'        => Codemaster::getIdByCode(2305),
                        'revert_reason_cause_id' => $validated['cause'],
                        'revert_reason_remarks'  => $validated['remark'],
                        'parent_id'      => AcceptRejectInfo::where('application_id', $id)
                            ->latest('id')
                            ->value('id') ?? null,
                    ]
                );
            }
            $this->clearSelected();
        }
    }
}
