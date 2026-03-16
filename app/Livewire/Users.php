<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\UserPersonal;
use App\Models\UserRoleSchemeOfficeMapping;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Attributes\Loggable;

class Users extends DataTableComponent
{
    public ?int $perPage = 5;
    public string $search = '';
    protected $listeners = ['userFilter' => 'userFilter'];
    public $role, $selectedMappingLevel, $selectedState, $selectedDistrict, $office, $scheme;

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setPaginationEnabled()
            ->setPerPageAccepted([5, 10, 25, 50])
            ->setPerPage($this->perPage)
            ->setPerPageVisibilityEnabled()
            ->setSearchEnabled()
            ->setSearchLive()
            ->setBulkActionsEnabled();

        $this->setHideBulkActionsWhenEmptyEnabled();

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
    public function userFilter($filters)
    {
        $this->scheme = $filters['scheme'] ?? null;
        $this->role = $filters['role'] ?? null;
        $this->selectedMappingLevel = $filters['mapping_level'] ?? null;
        $this->selectedState = $filters['state'] ?? null;
        $this->selectedDistrict = $filters['district'] ?? null;
        $this->office = $filters['office'] ?? null;
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
            Column::make("Id", "id")
                ->searchable(),
            Column::make("Name", "name")
                ->searchable(),
            Column::make("Mobile No", "mobile_no")
                ->searchable(),
            Column::make("Email", "email")
                ->searchable(),
            Column::make("Scheme Name", "id")
                ->format(function ($value, $row, Column $column) {
                    $schemes = $row->RoleSchemeOfficeMappings->pluck('scheme.name')->unique()->implode(', ');
                    return $schemes ?: 'N/A';
                }),

            Column::make('Actions')
                ->label(fn($row) => view('coulmn_button.ConfirmDeleteButton', [
                    'itemId' => $row->id,
                    'action' => 'delete',
                    'title' => 'Delete User',
                    'message' => "This is $row->name , are you want to delete this User?",
                    'tooltip' => 'Delete User',

                ])->render())
                ->html(),
        ];
    }

    public function builder(): Builder
    {
        $query = User::query()
            ->whereHas('RoleSchemeOfficeMappings.office', function ($q) {
                $q->where('is_active', 1);

                if (!empty($this->role)) {
                    $q->where('role_id', $this->role);
                }

                if (!empty($this->office)) {
                    $q->where('office_id', $this->office);
                }
                if (!empty($this->selectedDistrict)) {
                    $q->where('district_id', $this->selectedDistrict);
                }
                if (!empty($this->selectedMappingLevel)) {
                    $q->where('office_type_id', $this->selectedMappingLevel);
                }
                if (!empty($this->scheme)) {
                    $q->where('scheme_id', $this->scheme);
                }
            })
            ->with(['mappedRoles', 'mappedPermissions', 'RoleSchemeOfficeMappings.scheme'])
            ->orderBy('id', 'asc');
        return $query;
    }
    #[Loggable(level: 'C', nickname: 'Delete User')]
    public function delete($id)
    {
        $user = User::find($id);

        if ($user) {
            UserRoleSchemeOfficeMapping::where('user_id', $id)->delete();
            UserPersonal::where('user_id', $id)->delete();
            $user->delete();
        }

        $this->dispatch('toastr', [
            'type' => 'success',
            'message' => 'User deleted successfully!',
        ]);
    }
}
