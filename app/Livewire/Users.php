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

class Users extends DataTableComponent
{
    public ?int $perPage = 5;
    public string $search = '';
    protected $listeners = ['userFilter' => 'userFilter'];
    public $role, $selectedMappingLevel, $selectedState, $selectedDistrict, $office;

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
    public function userFilter($filters)
    {
        $this->role = $filters['role'] ?? null;
        $this->selectedMappingLevel = $filters['mapping_level'] ?? null;
        $this->selectedState = $filters['state'] ?? null;
        $this->selectedDistrict = $filters['district'] ?? null;
        $this->office = $filters['office'] ?? null;

        // Add any derived logic here if needed
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

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable()
                ->searchable(),
            Column::make("Name", "name")
                ->sortable()
                ->searchable(),
            Column::make("Mobile No", "mobile_no")
                ->sortable()
                ->searchable(),
            Column::make("Email", "email")
                ->sortable()
                ->searchable(),
        ];
    }

    public function builder(): Builder
    {
        $query = User::query()
            ->whereHas('RoleSchemeOfficeMappings', function ($q) {
                $q->where('is_active', 1);

                if (!empty($this->role)) {
                    $q->where('role_id', $this->role);
                }

                if (!empty($this->office)) {
                    $q->where('office_id', $this->office);
                }
            })
            ->orderBy('id', 'asc');

        return $query;
    }

    public function deleteUser($userId)
    {
        DB::transaction(function () use ($userId) {
            UserRoleSchemeOfficeMapping::where('user_id', $userId)->delete();
            UserPersonal::where('user_id', $userId)->delete();
            User::where('id', $userId)->delete();
        });

        Session::flash('success', 'User deleted successfully.');
    }
    public function render(): \Illuminate\View\View
    {
        return view('livewire.users-table', [
            'rows' => $this->getRows(),
        ]);
    }
}
